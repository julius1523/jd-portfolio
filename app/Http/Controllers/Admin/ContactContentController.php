<?php

namespace App\Http\Controllers\Admin;

use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\ContactContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function is_array;

class ContactContentController extends Controller
{
    public function __construct(
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

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'socials' => $data?->socials ?? [],
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