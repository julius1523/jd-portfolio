<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactContent extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'profile_image' => 'array',
            'socials' => 'array',
        ];
    }
}
