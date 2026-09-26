<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;
use function in_array;

class FileUploadService
{

    private const array COMPRESSIBLE_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const string PDF_MIME = 'application/pdf';

    /**
     * @param int $imageMaxWidth
     * @param int $imageQuality
     */
    public function __construct(
        private readonly int $imageMaxWidth = 1920,
        private readonly int $imageQuality = 75,
    ) {
    }

    public function handle(
        Request $request,
        object $model,
        string $field,
        string $column,
        string $path
    ): void {
        if ($request->hasFile($field)) {
            $file = $request->file($field);

            if (!$file->isValid()) {
                throw new RuntimeException('Uploaded file failed to transfer correctly.');
            }

            $oldFile = $model->{$column};
            $filename = $file->hashName();

            $this->storeCompressed($file, $path, $filename);

            $filePath = "$path/$filename";

            $model->{$column} = [
                'file_name' => (string) $filename,
                'orig_name' => $file->getClientOriginalName(),
                'file_size' => Storage::disk('s3')->size($filePath),
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

        if (in_array($mime, self::COMPRESSIBLE_IMAGE_MIMES, true)) {
            if ($this->compressImage($file, $path, $filename)) {
                return;
            }

            Log::warning('Image compression failed, storing original file.', [
                'filename' => $filename,
                'mime' => $mime,
            ]);
        }

        $file->storeAs($path, $filename, 'public');

        if ($mime === self::PDF_MIME) {
            $this->compressPdf(Storage::disk('s3')->path("$path/$filename"));
        }
    }

    private function compressImage(UploadedFile $file, string $path, string $filename): bool
    {
        try {
            $encoded = Image::decode($file)
                ->scaleDown(width: $this->imageMaxWidth)
                ->encodeUsingMediaType($file->getMimeType(), quality: $this->imageQuality);

            Storage::disk('s3')->put("$path/$filename", (string) $encoded);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Image encode/decode failed.', [
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function ghostscriptBinary(): string
    {
        if ($configured = config('services.ghostscript.binary')) {
            return $configured;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return 'gswin64c';
        }

        return 'gs';
    }

    private function compressPdf(string $fullPath): void
    {
        $tmp = "$fullPath.tmp";

        try {
            $result = Process::env([
                'TEMP' => sys_get_temp_dir(),
                'TMP' => sys_get_temp_dir(),
            ])->run([
                        $this->ghostscriptBinary(),
                        '-sDEVICE=pdfwrite',
                        '-dCompatibilityLevel=1.4',
                        '-dPDFSETTINGS=/ebook',
                        '-dNOPAUSE',
                        '-dQUIET',
                        '-dBATCH',
                        "-sOutputFile={$tmp}",
                        $fullPath,
                    ]);
        } catch (\Throwable $e) {
            Log::warning('Ghostscript could not be run; keeping uncompressed PDF.', [
                'error' => $e->getMessage(),
            ]);
            @unlink($tmp);
            return;
        }

        if (!$result->successful()) {
            Log::warning('Ghostscript PDF compression failed.', [
                'exit_code' => $result->exitCode(),
                'error' => $result->errorOutput(),
            ]);
            @unlink($tmp);
            return;
        }

        if (file_exists($tmp) && filesize($tmp) > 0 && filesize($tmp) < filesize($fullPath)) {
            rename($tmp, $fullPath);
        } else {
            @unlink($tmp);
        }
    }

    private function deleteOldFile(?array $file, string $path): void
    {
        if (!empty($file['file_name'])) {
            Storage::disk('s3')->delete("$path/{$file['file_name']}");
        }
    }
}