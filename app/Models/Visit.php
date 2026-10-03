<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

class Visit extends Model
{
    use MassPrunable;

    public $timestamps = false;
    protected $guarded = [];

    public function prunable(): Builder
    {
        return $this->newQuery()->where('created_at', '<', now()->subMonths(12));
    }
}