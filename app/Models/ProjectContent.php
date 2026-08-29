<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectContent extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'profile_image' => 'array',
            'projects' => 'array',
        ];
    }
}
