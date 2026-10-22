<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class RedirectSocialMediaPosts
{
    public function handle(Request $request, Closure $next)
    {
        // Control Panel
        if ($request->is('cp') || $request->is('cp/*')) {
            return $next($request);
        }

        // Resolve entry
        $uri = '/' . ltrim($request->getPathInfo(), '/');
        $entry = Entry::findByUri($uri, Site::current()->handle());

        if (! $entry || $entry->collectionHandle() !== 'posts') {
            return $next($request);
        }

        // Cek kategori sosial-media
        $isSocial = collect($entry->value('categories') ?? [])
            ->contains(fn ($slug) => (string) $slug === 'sosial-media');

        if (! $isSocial) {
            return $next($request);
        }

        // URL eksternal
        $rawLink = $entry->value('social_media_links');

        if (is_string($rawLink) && str_starts_with($rawLink, 'entry::')) {
            $target = Entry::find(str_replace('entry::', '', $rawLink))?->url();
        } else {
            $target = is_string($rawLink) ? $rawLink : null;
        }

        $target = is_string($target) ? trim($target) : '';

        // Redirect atau 404
        if ($target === '') {
            abort(404);
        }

        return redirect()->away($target, 301);
    }
}
