<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateProjectsContentRequest;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\ProjectContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\ArraySort;
use function array_slice;
use function count;

class ProjectsContentController extends Controller
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