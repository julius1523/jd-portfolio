<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'profile_image' => 'array',
            'random_facts' => 'array',
            'others' => 'array',
        ];
    }
}
