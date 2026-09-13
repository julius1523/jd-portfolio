<?php

namespace App\Http\Controllers\Admin;

use App\Services\IconResolverService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\ContactContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\ArraySort;
use function array_slice;
use function count;
use function is_array;

class ContactContentController extends Controller
{
    public function __construct(
        private IconResolverService $iconResolverService,
        private FileUploadService $fileUploadService
    ) {
    }

    public function getContactContent(Request $request)
    {
        $data = ContactContent::select([
            'profile_image',
            'heading',
            'description',
            'socials',
        ])->first();

        $socials = collect($data?->socials ?? [])
            ->map(function ($social) {
                $social['iconSvg'] = $this->iconResolverService->resolveSvg(
                    $social['icon'] ?? null
                );

                return $social;
            })
            ->all();

        $sorted = ArraySort::byKey(
            $socials,
            $request->query('sortBy'),
            $request->query('sortOrder', 'asc'),
            ['name']
        );

        $total = count($sorted);
        $perPage = (int) $request->query('perPage', 10);

        if ($perPage === -1) {
            $paged = $sorted;
        } else {
            $page = max((int) $request->query('page', 1), 1);
            $perPage = max($perPage, 1);

            $paged = array_slice(
                $sorted,
                ($page - 1) * $perPage,
                $perPage
            );
        }

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'socials' => $paged,
            'total' => $total,
        ]);
    }

    public function updateContactContent(Request $request)
    {
        $payload = json_decode($request->input('payload', '{}'), true);

        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid payload.'], 422);
        }

        $socialsInput = $payload['socials'] ?? [];

        $merged = [
            ...$payload,
            'profileImage' => $request->file('profileImage'),
        ];

        $validated = validator($merged, [
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'socials' => ['nullable', 'array'],
            'socials.*.name' => ['required', 'string', 'max:255'],
            'socials.*.linkUrl' => ['required', 'url'],
            'socials.*.icon' => ['required', 'string', 'max:100'],
        ])->validate();

        $data = ContactContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profileImage', 'profile_image', 'uploads/images');

        $socials = [];

        foreach ($socialsInput as $index => $social) {
            $existing = $data->socials[$index] ?? null;

            $socials[] = [
                'id' => $social['id'] ?? $existing['id'] ?? (string) Str::uuid(),
                'name' => $social['name'] ?? null,
                'linkUrl' => $social['linkUrl'] ?? null,
                'icon' => $social['icon'] ?? null,
            ];
        }

        $data->socials = $socials;
        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}