<?php

namespace App\Services;

use function in_array;
use function strlen;

class IconResolverService
{
    protected array $allowedSets;
    protected static array $loaded = [];

    public function __construct()
    {
        $this->allowedSets = config('icons.sets');
    }

    public function allowedSets(): array
    {
        return $this->allowedSets;
    }

    public function loadSet(string $set): array
    {
        return self::$loaded[$set] ??= json_decode(
            file_get_contents(resource_path("iconify/{$set}.json")),
            true
        );
    }

    public function toSvg(array $data, array $icon): string
    {
        $width = $icon['width'] ?? $data['width'] ?? 24;
        $height = $icon['height'] ?? $data['height'] ?? 24;

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$width} {$height}\" width=\"1em\" height=\"1em\" fill=\"currentColor\" preserveAspectRatio=\"xMidYMid meet\" style=\"display:block; margin:auto;\">{$icon['body']}</svg>";
    }

    public function toName(string $set, string $name): string
    {
        return "i-{$set}-{$name}";
    }

    public function parseName(string $full): ?array
    {
        if (str_contains($full, ':')) {
            [$set, $name] = explode(':', $full, 2);
            return in_array($set, $this->allowedSets) ? [$set, $name] : null;
        }

        if (!str_starts_with($full, 'i-')) {
            return null;
        }

        $rest = substr($full, 2);
        foreach ($this->allowedSets as $set) {
            $prefix = "{$set}-";
            if (str_starts_with($rest, $prefix)) {
                $name = substr($rest, strlen($prefix));
                return $name !== '' ? [$set, $name] : null;
            }
        }

        return null;
    }

    public function resolveSvg(?string $rawName): ?string
    {
        if (!$rawName) {
            return null;
        }

        $parsed = $this->parseName($rawName);
        if (!$parsed) {
            return null;
        }

        [$set, $name] = $parsed;
        $data = $this->loadSet($set);
        $icon = $data['icons'][$name] ?? null;

        return $icon ? $this->toSvg($data, $icon) : null;
    }
}
