<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProjectsContentRequest;
use App\Models\ProjectContent;
use App\Services\FileUploadService;
use Illuminate\Support\Str;

class ProjectsContentController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getProjectContent()
    {
        $data = ProjectContent::select([
            'profile_image',
            'heading',
            'description',
            'projects',
        ])->first();

        $order = array_flip(UpdateProjectsContentRequest::CATEGORIES);
        $projects = $data?->projects ?? [];

        usort($projects, fn($a, $b) => [
            $order[$a['category'] ?? ''] ?? PHP_INT_MAX,
            mb_strtolower($a['name'] ?? ''),
        ] <=> [
            $order[$b['category'] ?? ''] ?? PHP_INT_MAX,
            mb_strtolower($b['name'] ?? ''),
        ]);

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'description' => $data?->description,
            'projects' => $projects,
        ]);
    }

    public function updateProjectContent(UpdateProjectsContentRequest $request)
    {
        $validated = $request->validated();
        $projectsInput = $request->input('projects', []);

        $data = ProjectContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'description' => $validated['description'] ?? $data->description,
        ]);

        $this->fileUploadService->handle($request, $data, 'profileImage', 'profile_image', 'uploads/images');

        $existingById = collect($data->projects ?? [])->keyBy('id');

        $projects = [];

        foreach ($projectsInput as $index => $project) {
            $existing = $existingById->get($project['id'] ?? '');

            $imageHolder = new \stdClass();
            $imageHolder->image = $existing['image'] ?? null;
            $this->fileUploadService->handle($request, $imageHolder, "projectImages.$index", 'image', 'uploads/images');

            $linkType = $project['linkType'] ?? 'link';

            $linkFileHolder = new \stdClass();
            $linkFileHolder->file = $existing['linkFile'] ?? null;
            if ($linkType === 'upload') {
                $this->fileUploadService->handle($request, $linkFileHolder, "projectLinkFiles.$index", 'file', 'uploads/files');
            } else {
                $this->fileUploadService->deleteOldFile($linkFileHolder->file, 'uploads/files');
                $linkFileHolder->file = null;
            }

            $projects[] = [
                'id' => $existing['id'] ?? (string) Str::uuid7(),
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

        $kept = array_flip(array_column($projects, 'id'));

        foreach ($existingById as $id => $old) {
            if (!isset($kept[$id])) {
                $this->fileUploadService->deleteOldFile($old['image'] ?? null, 'uploads/images');
                $this->fileUploadService->deleteOldFile($old['linkFile'] ?? null, 'uploads/files');
            }
        }

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}