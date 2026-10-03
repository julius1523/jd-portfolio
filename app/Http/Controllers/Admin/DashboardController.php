<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function getDashboard()
    {
        return [
            'daily' => Visit::selectRaw('DATE(created_at) as day, COUNT(*) as views')
                ->where('created_at', '>=', now()->subDays(90))
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
        ];
    }
}