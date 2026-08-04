<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

/**
 * Phase 1 i18n toggle endpoint.
 *
 * Persists the selected locale to session (guests) or session + users.locale
 * (authed), then redirects back to the locale-prefixed equivalent of whatever
 * URL the visitor just came from. URLs are rewritten to keep the user on the
 * page they were viewing — e.g. POST /locale/en from /en/packages rewrites
 * back to /packages (ID) rather than bouncing them home.
 */
class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            abort(404);
        }

        Session::put('locale', $locale);

        $user = $request->user();
        if ($user !== null) {
            $user->forceFill(['locale' => $locale])->save();
        }

        return redirect($this->localizedPreviousUrl($request, $locale));
    }

    /**
     * Build the redirect target by rewriting the previous URL's locale prefix
     * so the toggle is "stay on this page, just translate it".
     */
    private function localizedPreviousUrl(Request $request, string $locale): string
    {
        $previous = $request->session()->previousUrl() ?? url('/');

        // Strip scheme/host, keep only path + query.
        $parsed = parse_url($previous);
        if (! is_array($parsed) || ! array_key_exists('path', $parsed)) {
            return $locale === 'en' ? URL::to('/en') : URL::to('/');
        }

        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? '';

        // Normalise leading slash then strip any existing locale segment.
        $path = '/'.ltrim($path, '/');
        if (str_starts_with($path, '/en/') || $path === '/en') {
            $path = substr($path, 3); // remove '/en'
            if ($path === '') {
                $path = '/';
            }
        }

        // Re-apply the English prefix when switching to English. Indonesian
        // stays at the root.
        if ($locale === 'en') {
            $path = '/en'.($path === '/' ? '' : $path);
        }

        $target = rtrim(URL::to($path), '/') ?: URL::to('/');

        return $query !== '' ? $target.'?'.$query : $target;
    }
}
