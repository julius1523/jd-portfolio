<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackVisitController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'visitor_id' => 'required|uuid',
            'session_id' => 'required|uuid',
            'path' => ['required', 'string', Rule::in(['/home', '/about', '/projects', '/contact'])],
            'referrer' => 'nullable|string|max:500',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
        ]);

        $ua = (string) $request->userAgent();

        if (preg_match('/bot|crawl|spider|slurp|headless|lighthouse/i', $ua)) {
            return response()->noContent();
        }

        Visit::create([
            ...$data,
            'ip_hash' => hash('sha256', $request->ip() . $ua . now()->toDateString() . config('app.key')),
            'country' => $request->header('CF-IPCountry'),
            'device_type' => preg_match('/mobile|android|iphone/i', $ua) ? 'mobile' : 'desktop',
        ]);

        return response()->noContent();
    }
}