<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;

class ComingSoonMode
{
    public function handle(Request $request, Closure $next)
    {
        // Akses Control Panel
        if ($request->is('cp') || $request->is('cp/*')) {
            return $next($request);
        }

        // Bypass admin login
        if (auth()->check()) {
            return $next($request);
        }

        $global = GlobalSet::findByHandle('page_preparation')
            ?->in(Site::current()->handle());

        $active = $global?->get('activate_mode') ?? false;

        // Durasi habis
        if ($active && $this->durationHasPassed($global->get('duration'))) {
            $global->set('activate_mode', false)->save();
            $active = false;
        }

        if ($active) {
            return response()->view('coming-soon', [
                'global' => $global,
            ], 503);
        }

        return $next($request);
    }

    protected function durationHasPassed($duration): bool
    {
        if (blank($duration)) {
            return false;
        }

        try {
            return Carbon::parse($duration)->isPast();
        } catch (Exception $e) {
            return false;
        }
    }
}