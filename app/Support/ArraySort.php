<?php

namespace App\Support;

use function count;
use function in_array;

class ArraySort
{
    public static function byKey(
        array $items,
        ?string $sortBy,
        string $sortOrder = 'asc',
        array $sortableColumns = [],
        bool $numeric = false
    ): array {
        if (!$sortBy || !in_array($sortBy, $sortableColumns, true) || count($items) < 2) {
            return $items;
        }

        usort($items, function ($a, $b) use ($sortBy, $numeric) {
            $valueA = $a[$sortBy] ?? null;
            $valueB = $b[$sortBy] ?? null;

            return $numeric
                ? ($valueA <=> $valueB)
                : strcasecmp((string) $valueA, (string) $valueB);
        });

        return strtolower($sortOrder) === 'desc' ? array_values(array_reverse($items)) : array_values($items);
    }
}