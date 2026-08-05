<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $cv_path
 * @property string|null $image_path
 */
class HomeContent extends Model
{

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'title' => 'array',
        ];
    }

    protected $appends = [
        'cv_url',
        'image_url',
    ];

    protected function cvUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->cv_path
            ? Storage::url("uploads/files/{$this->cv_path}")
            : null,
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_path
            ? Storage::url("uploads/images/{$this->image_path}")
            : null,
        );
    }
}