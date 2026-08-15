<?php

namespace App\Http\Controllers\Admin;

use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Http\Request;

class HomeContentController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getHomeContent()
    {
        return response()->json(
            HomeContent::select([
                'greeting',
                'title',
                'description',
                'primary_btn_text',
                'primary_btn_link',
                'secondary_btn_text',
                'file',
                'image',
            ])->first()
        );
    }

    public function updateHomeContent(Request $request)
    {
        $validated = $request->validate([
            'greeting' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'array', 'min:1', 'max:4'],
            'description' => ['nullable', 'string'],
            'primary_btn_text' => ['nullable', 'string', 'max:255'],
            'primary_btn_link' => ['nullable', 'string', 'max:255'],
            'secondary_btn_text' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'remove_file' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $data = HomeContent::firstOrCreate([]);

        $data->fill(collect($validated)->except(['file', 'image', 'remove_file', 'remove_image'])->toArray());

        $this->fileUploadService->handle($request, $data, 'file', 'file', 'uploads/files');
        $this->fileUploadService->handle($request, $data, 'image', 'image', 'uploads/images');

        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}