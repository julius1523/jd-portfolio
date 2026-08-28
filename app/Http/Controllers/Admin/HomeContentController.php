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
        $data = HomeContent::select([
            'profile_image',
            'heading',
            'subheading',
            'description',
            'primary_btn_text',
            'primary_btn_link',
            'secondary_btn_text',
            'secondary_btn_file',
        ])->first();

        return response()->json([
            'profileImage' => $data?->profile_image,
            'heading' => $data?->heading,
            'subheading' => $data?->subheading ?? [],
            'description' => $data?->description,
            'primaryBtnText' => $data?->primary_btn_text,
            'primaryBtnLink' => $data?->primary_btn_link,
            'secondaryBtnText' => $data?->secondary_btn_text,
            'secondaryBtnFile' => $data?->secondary_btn_file,
        ]);
    }

    public function updateHomeContent(Request $request)
    {
        $validated = $request->validate([
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'array', 'min:1', 'max:4'],
            'subheading.*' => ['string'],
            'description' => ['nullable', 'string'],
            'primaryBtnText' => ['nullable', 'string', 'max:255'],
            'primaryBtnLink' => ['nullable', 'string', 'max:255'],
            'secondaryBtnText' => ['nullable', 'string', 'max:255'],
            'secondaryBtnFile' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $data = HomeContent::firstOrCreate([]);

        $data->fill([
            'heading' => $validated['heading'] ?? $data->heading,
            'subheading' => $validated['subheading'] ?? $data->subheading,
            'description' => $validated['description'] ?? $data->description,
            'primary_btn_text' => $validated['primaryBtnText'] ?? $data->primary_btn_text,
            'primary_btn_link' => $validated['primaryBtnLink'] ?? $data->primary_btn_link,
            'secondary_btn_text' => $validated['secondaryBtnText'] ?? $data->secondary_btn_text,
        ]);

        $this->fileUploadService->handle($request, $data, 'secondaryBtnFile', 'secondary_btn_file', 'uploads/files');
        $this->fileUploadService->handle($request, $data, 'profileImage', 'profile_image', 'uploads/images');

        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }
}