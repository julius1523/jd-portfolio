<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends Controller
{
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
                'cv_path',
                'image_path',
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
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'remove_cv' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $data = HomeContent::firstOrCreate([]);

        $data->fill(collect($validated)->except(['cv', 'image', 'remove_cv', 'remove_image'])->toArray());

        $this->handleFileUpload($request, $data, 'cv', 'cv_path', 'uploads/files');
        $this->handleFileUpload($request, $data, 'image', 'image_path', 'uploads/images');

        $data->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $data,
        ]);
    }

    private function handleFileUpload(Request $request, HomeContent $data, string $field, string $column, string $path): void
    {
        if ($request->hasFile($field)) {
            $oldFilename = $data->{$column};

            $file = $request->file($field);
            $filename = $file->hashName();
            $file->storeAs($path, $filename, 'public');

            $data->{$column} = $filename;

            if ($oldFilename) {
                Storage::disk('public')->delete("$path/$oldFilename");
            }
            return;
        }

        if ($request->boolean("remove_{$field}")) {
            $oldFilename = $data->{$column};

            if ($oldFilename) {
                Storage::disk('public')->delete("$path/$oldFilename");
            }

            $data->{$column} = null;
        }
    }
}