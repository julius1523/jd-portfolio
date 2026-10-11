<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateContactContentRequest;
use App\Services\IconResolverService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\ContactContent;
use Illuminate\Support\Str;
use App\Support\ArraySort;

class ContactContentController extends Controller
{
    public function __construct(
        private IconResolverService $iconResolverService,
        private FileUploadService $fileUploadService
    ) {
    }

    public function getContactContent()
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

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'socials' => array_values(ArraySort::byKey($socials, null, 'asc', ['name'])),
        ]);
    }

    public function updateContactContent(UpdateContactContentRequest $request)
    {
        $validated = $request->validated();

        $socialsInput = $request->input('socials', []);

        $data = ContactContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profileImage', 'profile_image', 'uploads/images');

        $existingById = collect($data->socials ?? [])->keyBy('id');

        $socials = [];

        foreach ($socialsInput as $social) {
            $existing = $existingById->get($social['id'] ?? '');

            $socials[] = [
                'id' => $existing['id'] ?? (string) Str::uuid7(),
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