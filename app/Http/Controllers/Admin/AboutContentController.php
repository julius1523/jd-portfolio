<?php

namespace App\Http\Controllers\Admin;

use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;
use function is_array;

class AboutContentController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getAboutContent()
    {
        $data = AboutContent::select([
            'profile_image',
            'heading',
            'description',
            'skills',
            'random_facts',
            'others',
        ])->first();

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'skills' => $data?->skills ?? [],
            'randomFacts' => $data?->random_facts ?? [],
            'others' => [
                'title' => $data?->others['title'] ?? null,
                'description' => $data?->others['description'] ?? null,
                'image' => $data?->others['image'] ?? null,
            ],
        ]);
    }

    public function updateAboutContent(Request $request)
    {
        $payload = json_decode($request->input('payload', '{}'), true);

        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid payload.'], 422);
        }

        $merged = [
            ...$payload,
            'profile_image' => $request->file('profile_image'),
            'others_image' => $request->file('others_image'),
        ];

        $validated = validator($merged, [
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['array'],
            'skills.*.*' => ['string'],
            'randomFacts' => ['nullable', 'array'],
            'randomFacts.*' => ['string'],
            'others' => ['nullable', 'array'],
            'others.title' => ['nullable', 'string', 'max:255'],
            'others.description' => ['nullable', 'string'],
            'others_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ])->validate();

        $data = AboutContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
            'skills' => $validated['skills'] ?? $data->skills,
            'random_facts' => $validated['randomFacts'] ?? $data->random_facts,
        ]);

        $this->fileUploadService->handle($request, $data, 'profile_image', 'profile_image', 'uploads/images');

        $others = $data->others ?? [];
        $others['title'] = $validated['others']['title'] ?? ($others['title'] ?? null);
        $others['description'] = $validated['others']['description'] ?? ($others['description'] ?? null);

        $imageHolder = new \stdClass();
        $imageHolder->image = $others['image'] ?? null;

        $this->fileUploadService->handle($request, $imageHolder, 'others_image', 'image', 'uploads/images');

        $others['image'] = $imageHolder->image;
        $data->others = $others;

        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}
