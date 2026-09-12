<?php

namespace App\Http\Controllers\Admin;

use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\ProjectContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Support\ArraySort;
use function array_slice;
use function count;
use function is_array;

class ProjectContentController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getProjectContent(Request $request)
    {
        $data = ProjectContent::select([
            'profile_image',
            'heading',
            'description',
            'projects',
        ])->first();

        $sorted = ArraySort::byKey(
            $data?->projects ?? [],
            $request->query('sortBy'),
            $request->query('sortOrder', 'asc'),
            ['category', 'name']
        );

        $total = count($sorted);
        $perPage = (int) $request->query('perPage', 10);

        if ($perPage === -1) {
            $paged = $sorted;
        } else {
            $page = max((int) $request->query('page', 1), 1);
            $perPage = max($perPage, 1);
            $paged = array_slice($sorted, ($page - 1) * $perPage, $perPage);
        }

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'projects' => $paged,
            'total' => $total,
        ]);
    }

    public function updateProjectContent(Request $request)
    {
        $payload = json_decode($request->input('payload', '{}'), true);

        if (!is_array($payload)) {
            return response()->json(['message' => 'Invalid payload.'], 422);
        }

        $projectsInput = $payload['projects'] ?? [];

        $merged = [
            ...$payload,
            'profileImage' => $request->file('profileImage'),
            'projectImages' => $request->file('projectImages') ?? [],
            'projectLinkFiles' => $request->file('projectLinkFiles') ?? [],
        ];

        $validated = validator($merged, [
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'projects' => ['nullable', 'array'],
            'projects.*.category' => [
                'required',
                'string',
                Rule::in([
                    'Software Development',
                    'Technical Documentation',
                    'Presentations/Multimedia',
                ])
            ],
            'projects.*.name' => ['required', 'string', 'max:255'],
            'projects.*.description' => ['required', 'string'],
            'projects.*.materials' => ['required', 'array', 'min:1'],
            'projects.*.materials.*' => ['string', 'max:100'],

            'projects.*.linkType' => ['required', 'in:upload,link'],
            'projects.*.linkUrl' => ['nullable', 'url', 'required_if:projects.*.linkType,link'],

            'projectImages' => ['nullable', 'array'],
            'projectImages.*' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'projectLinkFiles' => ['nullable', 'array'],
            'projectLinkFiles.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip', 'max:20480'],
        ])->validate();

        $data = ProjectContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profileImage', 'profile_image', 'uploads/images');

        $projects = [];

        foreach ($projectsInput as $index => $project) {
            $existing = $data->projects[$index] ?? null;

            $imageHolder = new \stdClass();
            $imageHolder->image = $project['image'] ?? $existing['image'] ?? null;
            $this->fileUploadService->handle($request, $imageHolder, "projectImages.$index", 'image', 'uploads/images');

            $linkType = $project['linkType'] ?? 'link';

            $linkFileHolder = new \stdClass();
            $linkFileHolder->file = $project['linkFile'] ?? $existing['linkFile'] ?? null;
            if ($linkType === 'upload') {
                $this->fileUploadService->handle($request, $linkFileHolder, "projectLinkFiles.$index", 'file', 'uploads/files');
            } else {
                $linkFileHolder->file = null;
            }

            $projects[] = [
                'id' => $project['id'] ?? $existing['id'] ?? (string) Str::uuid(),
                'category' => $project['category'] ?? null,
                'name' => $project['name'] ?? null,
                'description' => $project['description'] ?? null,
                'materials' => $project['materials'] ?? [],
                'image' => $imageHolder->image,
                'linkType' => $linkType,
                'linkFile' => $linkType === 'upload' ? $linkFileHolder->file : null,
                'linkUrl' => $linkType === 'link' ? ($project['linkUrl'] ?? null) : null,
            ];
        }

        $data->projects = $projects;
        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}