<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'subheading' => 'array',
            'secondary_btn_file' => 'array',
            'profile_image' => 'array',
        ];
    }
}