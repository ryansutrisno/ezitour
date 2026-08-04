<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 1 i18n locale resolver.
 *
 * Resolution priority (highest first):
 *  1. Authenticated user's saved `locale` column.
 *  2. Session 'locale' key (set by LocaleController::switch).
 *  3. The URL path — requests under `/en/` are English; everything else
 *     falls through to Indonesian. (We deliberately do NOT read this from
 *     `$request->route()->parameter('locale')` because routes are defined
 *     twice — once at the root, once under the `en/` prefix — and only one
 *     of those registrations matches per request.)
 *  4. The Accept-Language HTTP header (en* → 'en', otherwise 'id').
 *  5. Fallback to the app default ('id').
 *
 * Side effects:
 *  - Sets the active app locale via App::setLocale(). App\Routing\
 *    UrlGenerator reads this to decide whether to emit `/en/…` URLs.
 *  - Persists the resolved locale to session so the next request (which
 *    might not carry an explicit URL signal, e.g. POST endpoints) keeps
 *    the same language.
 */
class SetLocale
{
    public const SUPPORTED = ['id', 'en'];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        // Mirror the resolution into the session so POST endpoints (which
        // don't carry a URL prefix signal) and downstream views see the same
        // locale as the originating GET.
        if (! Session::has('locale') || Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }

    /**
     * Apply the documented priority chain. Public for testability.
     */
    public function resolveLocale(Request $request): string
    {
        // (1) Authenticated user preference (highest priority — manual override
        // persisted via the profile/switch flow).
        $user = $request->user();
        if ($user !== null && filled($user->locale) && $this->isSupported($user->locale)) {
            return $user->locale;
        }

        // (2) Session-stored preference (covers guests who toggled via the
        // navbar switch, and POST endpoints that come after a GET).
        $sessionLocale = Session::get('locale');
        if (is_string($sessionLocale) && $this->isSupported($sessionLocale)) {
            return $sessionLocale;
        }

        // (3) URL path — `/en/…` is the English tree, anything else is ID.
        $path = '/'.ltrim($request->path(), '/');
        if ($path === '/en' || str_starts_with($path, '/en/')) {
            return 'en';
        }

        // (4) Accept-Language header auto-detection. English family → 'en';
        // anything else (incl. unspecified) falls through to Indonesian.
        $accept = $request->headers->get('Accept-Language');
        if (is_string($accept) && (str_starts_with(strtolower($accept), 'en') || preg_match('/^en[-_,]/i', $accept))) {
            return 'en';
        }

        // (5) App default.
        return 'id';
    }

    private function isSupported(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }
}
