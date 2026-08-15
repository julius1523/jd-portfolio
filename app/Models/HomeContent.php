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
            'file' => 'array',
            'image' => 'array',
        ];
    }
}