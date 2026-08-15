<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class FileUploadService
{
    public function handle(
        Request $request,
        object $model,
        string $field,
        string $column,
        string $path
    ): void {
        if ($request->hasFile($field)) {
            $oldFile = $model->{$column};
            $file = $request->file($field);
            $filename = $file->hashName();

            $this->storeCompressed($file, $path, $filename);

            $filePath = "$path/$filename";

            $model->{$column} = [
                'file_name' => (string) $filename,
                'orig_name' => $file->getClientOriginalName(),
                'file_size' => Storage::disk('public')->size($filePath),
                'mime_type' => $file->getMimeType(),
                'url' => Storage::url($filePath),
            ];

            $this->deleteOldFile($oldFile, $path);
            return;
        }

        if ($request->boolean("remove_{$field}")) {
            $oldFile = $model->{$column};
            $this->deleteOldFile($oldFile, $path);
            $model->{$column} = null;
        }
    }

    private function storeCompressed(UploadedFile $file, string $path, string $filename): void
    {
        $mime = $file->getMimeType();
        $fullPath = Storage::disk('public')->path("$path/$filename");

        if (str_starts_with($mime, 'image/')) {
            $image = Image::decode($file)
                ->scaleDown(width: 1920);

            Storage::disk('public')->put(
                "$path/$filename",
                (string) $image->encodeUsingFileExtension($file->getClientOriginalExtension(), quality: 75)
            );

            return;
        }

        if ($mime === 'application/pdf') {
            $file->storeAs($path, $filename, 'public');
            $this->compressPdf($fullPath);
            return;
        }

        $file->storeAs($path, $filename, 'public');
    }

    private function compressPdf(string $fullPath): void
    {
        $tmp = "$fullPath.tmp";

        $result = Process::run([
            'gs',
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            '-dPDFSETTINGS=/ebook',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            "-sOutputFile={$tmp}",
            $fullPath,
        ]);

        if ($result->successful() && file_exists($tmp)) {
            rename($tmp, $fullPath);
        }
    }

    private function deleteOldFile(?array $file, string $path): void
    {
        if (!empty($file['file_name'])) {
            Storage::disk('public')->delete("$path/{$file['file_name']}");
        }
    }
}