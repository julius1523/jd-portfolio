<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateAboutContentRequest;
use App\Services\IconResolverService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Support\Str;
use App\Support\ArraySort;

class AboutContentController extends Controller
{
    public function __construct(
        private IconResolverService $iconResolverService,
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

        $skills = collect($data?->skills ?? [])->map(function ($skill) {
            $skill['iconSvg'] = $this->iconResolverService->resolveSvg($skill['icon'] ?? null);
            return $skill;
        })->all();

        $randomFacts = collect($data?->random_facts ?? [])->map(function ($fact) {
            $fact['iconSvg'] = $this->iconResolverService->resolveSvg($fact['icon'] ?? null);
            return $fact;
        })->all();

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'skills' => array_values(ArraySort::byKey($skills, null, 'asc', ['category'])),
            'randomFacts' => array_values(ArraySort::byKey($randomFacts, null, 'asc', ['randomFact'])),
            'others' => [
                'title' => $data?->others['title'] ?? null,
                'description' => $data?->others['description'] ?? null,
                'image' => $data?->others['image'] ?? null,
            ],
        ]);
    }

    public function updateAboutContent(UpdateAboutContentRequest $request)
    {
        $validated = $request->validated();

        $skillsInput = $request->input('skills', []);
        $randomFactsInput = $request->input('randomFacts', []);

        $data = AboutContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profile_image', 'profile_image', 'uploads/images');

        $skillsById = collect($data->skills ?? [])->keyBy('id');
        $factsById = collect($data->random_facts ?? [])->keyBy('id');

        $skills = [];

        foreach ($skillsInput as $skill) {
            $existing = $skillsById->get($skill['id'] ?? '');

            $skills[] = [
                'id' => $existing['id'] ?? (string) Str::uuid7(),
                'category' => $skill['category'] ?? null,
                'skill' => array_values($skill['skill'] ?? []),
                'icon' => $skill['icon'] ?? null,
            ];
        }

        $data->skills = $skills;

        $randomFacts = [];

        foreach ($randomFactsInput as $fact) {
            $existing = $factsById->get($fact['id'] ?? '');

            $randomFacts[] = [
                'id' => $existing['id'] ?? (string) Str::uuid7(),
                'icon' => $fact['icon'] ?? null,
                'randomFact' => $fact['randomFact'] ?? null,
            ];
        }

        $data->random_facts = $randomFacts;

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