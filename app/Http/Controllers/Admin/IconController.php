<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\IconResolverService;
use Illuminate\Http\Request;
use function array_slice;
use function count;

class IconController extends Controller
{
    public function __construct(
        private IconResolverService $icons
    ) {
    }

    protected function setsFromRequest(Request $request): array
    {
        $allowed = $this->icons->allowedSets();
        $requested = array_filter(explode(',', $request->query('sets', implode(',', $allowed))));
        return array_values(array_intersect($requested, $allowed)) ?: $allowed;
    }

    public function getIcons(Request $request)
    {
        $sets = $this->setsFromRequest($request);
        $search = $request->query('search', '');

        $all = [];
        foreach ($sets as $set) {
            $data = $this->icons->loadSet($set);
            foreach (array_keys($data['icons']) as $name) {
                if ($search === '' || str_contains($name, $search)) {
                    $all[] = "{$set}:{$name}";
                }
            }
        }
        sort($all);

        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 60);
        $slice = array_slice($all, ($page - 1) * $perPage, $perPage);

        $items = array_map(function ($full) {
            [$set, $name] = explode(':', $full, 2);
            $data = $this->icons->loadSet($set);
            return [
                'name' => $this->icons->toName($set, $name),
                'svg' => $this->icons->toSvg($data, $data['icons'][$name]),
            ];
        }, $slice);

        return response()->json([
            'data' => $items,
            'total' => count($all),
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }
}