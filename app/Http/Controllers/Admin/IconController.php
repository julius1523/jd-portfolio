<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use function count;
use function in_array;

class IconController extends Controller
{
    protected array $allowedSets = ['mdi', 'ri'];

    protected function loadSet(string $set): array
    {
        return Cache::rememberForever("iconify:{$set}", function () use ($set) {
            $path = base_path("node_modules/@iconify-json/{$set}/icons.json");
            return json_decode(file_get_contents($path), true);
        });
    }

    protected function toSvg(array $data, array $icon): string
    {
        $viewBox = "0 0 {$data['width']} {$data['height']}";
        return "<svg viewBox=\"{$viewBox}\" fill=\"currentColor\">{$icon['body']}</svg>";
    }

    protected function setsFromRequest(Request $request): array
    {
        $requested = array_filter(explode(',', $request->query('sets', 'mdi,ri')));
        return array_values(array_intersect($requested, $this->allowedSets)) ?: $this->allowedSets;
    }

    public function index(Request $request)
    {
        $sets = $this->setsFromRequest($request);
        $search = $request->query('search', '');

        $all = [];
        foreach ($sets as $set) {
            $data = $this->loadSet($set);
            foreach (array_keys($data['icons']) as $name) {
                if ($search === '' || str_contains($name, $search)) {
                    $all[] = "{$set}:{$name}";
                }
            }
        }
        sort($all);

        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 48);
        $slice = array_slice($all, ($page - 1) * $perPage, $perPage);

        $items = array_map(function ($full) {
            [$set, $name] = explode(':', $full, 2);
            $data = $this->loadSet($set);
            return [
                'name' => $full,
                'svg' => $this->toSvg($data, $data['icons'][$name]),
            ];
        }, $slice);

        return response()->json([
            'data' => $items,
            'total' => count($all),
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    public function show(Request $request)
    {
        $full = $request->query('name', '');
        if (!str_contains($full, ':')) {
            return response()->json(['message' => 'Invalid icon name'], 422);
        }
        [$set, $name] = explode(':', $full, 2);

        if (!in_array($set, $this->allowedSets)) {
            return response()->json(['message' => 'Unknown set'], 422);
        }

        $data = $this->loadSet($set);
        if (!isset($data['icons'][$name])) {
            return response()->json(['message' => 'Icon not found'], 404);
        }

        return response()->json([
            'name' => $full,
            'svg' => $this->toSvg($data, $data['icons'][$name]),
        ]);
    }
}
