<?php

namespace App\Http\Controllers\Admin;

use App\Services\IconResolverService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\ArraySort;
use function array_slice;
use function count;
use function is_array;

class AboutContentController extends Controller
{
    public function __construct(
        private IconResolverService $iconResolverService,
        private FileUploadService $fileUploadService
    ) {
    }

    public function getAboutContent(Request $request)
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

        $skillsSorted = ArraySort::byKey(
            $skills,
            $request->query('skillsSortBy'),
            $request->query('skillsSortOrder', 'asc'),
            ['category']
        );

        $skillsTotal = count($skillsSorted);
        $skillsPerPage = (int) $request->query('skillsPerPage', 10);

        if ($skillsPerPage === -1) {
            $skillsPaged = $skillsSorted;
            $skillsPage = 1;
        } else {
            $skillsPage = max((int) $request->query('skillsPage', 1), 1);
            $skillsPerPage = max($skillsPerPage, 1);

            $skillsPaged = array_slice(
                $skillsSorted,
                ($skillsPage - 1) * $skillsPerPage,
                $skillsPerPage
            );
        }

        $randomFactsSorted = ArraySort::byKey(
            $randomFacts,
            $request->query('randomFactsSortBy'),
            $request->query('randomFactsSortOrder', 'asc'),
            ['randomFact']
        );

        $randomFactsTotal = count($randomFactsSorted);
        $randomFactsPerPage = (int) $request->query('randomFactsPerPage', 10);

        if ($randomFactsPerPage === -1) {
            $randomFactsPaged = $randomFactsSorted;
            $randomFactsPage = 1;
        } else {
            $randomFactsPage = max((int) $request->query('randomFactsPage', 1), 1);
            $randomFactsPerPage = max($randomFactsPerPage, 1);

            $randomFactsPaged = array_slice(
                $randomFactsSorted,
                ($randomFactsPage - 1) * $randomFactsPerPage,
                $randomFactsPerPage
            );
        }

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'skills' => $skillsPaged,
            'skillsMeta' => [
                'total' => $skillsTotal,
                'perPage' => $skillsPerPage,
                'page' => $skillsPage,
            ],
            'randomFacts' => $randomFactsPaged,
            'randomFactsMeta' => [
                'total' => $randomFactsTotal,
                'perPage' => $randomFactsPerPage,
                'page' => $randomFactsPage,
            ],
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

        $skillsInput = $payload['skills'] ?? [];
        $randomFactsInput = $payload['randomFacts'] ?? [];

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
            'skills.*.category' => ['required', 'string', 'max:150'],
            'skills.*.skill' => ['required', 'array', 'min:1'],
            'skills.*.skill.*' => ['required', 'string', 'max:150'],
            'skills.*.icon' => ['nullable', 'string', 'max:150'],
            'randomFacts' => ['nullable', 'array'],
            'randomFacts.*.icon' => ['required', 'string', 'max:150'],
            'randomFacts.*.randomFact' => ['required', 'string'],
            'others' => ['nullable', 'array'],
            'others.title' => ['nullable', 'string', 'max:255'],
            'others.description' => ['nullable', 'string'],
            'others_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ])->validate();

        $data = AboutContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profile_image', 'profile_image', 'uploads/images');

        $skills = [];

        foreach ($skillsInput as $index => $skill) {
            $existing = $data->skills[$index] ?? null;

            $skills[] = [
                'id' => $skill['id'] ?? $existing['id'] ?? (string) Str::uuid(),
                'category' => $skill['category'] ?? null,
                'skill' => array_values($skill['skill'] ?? []),
                'icon' => $skill['icon'] ?? null,
            ];
        }

        $data->skills = $skills;

        $randomFacts = [];

        foreach ($randomFactsInput as $index => $fact) {
            $existing = $data->random_facts[$index] ?? null;

            $randomFacts[] = [
                'id' => $fact['id'] ?? $existing['id'] ?? (string) Str::uuid(),
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