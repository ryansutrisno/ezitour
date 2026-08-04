<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

/**
 * Locale-aware URL generator for Phase 1 i18n.
 *
 * Indonesian routes are registered at the root path with their original
 * names (`front.home`, `front.packages.show`, …). English routes mirror
 * them under the `/en/` prefix with the `en.` name namespace
 * (`en.front.home`, `en.front.packages.show`, …).
 *
 * This subclass transparently switches the route name to its `en.`
 * namespace variant when the active app locale is English, so blade
 * templates can keep calling `route('front.home')` regardless of locale
 * and still emit the correct prefix.
 *
 * Routes that don't have an `en.` variant (e.g. `front.locale.switch`,
 * `midtrans.notification`) fall through to the originally requested name.
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * Resolve the route URL, swapping in the `en.` namespace variant when
     * the active locale is English.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        $localizedName = $this->localizeName($name);

        if ($localizedName !== $name && ! is_null($this->routes->getByName($localizedName))) {
            return parent::route($localizedName, $parameters, $absolute);
        }

        return parent::route($name, $parameters, $absolute);
    }

    /**
     * Prefix a route name with `en.` when the active locale is English and
     * the name isn't already namespaced / explicitly excluded.
     */
    private function localizeName(string $name): string
    {
        if (app()->getLocale() !== 'en') {
            return $name;
        }

        // Already localized — leave alone.
        if (str_starts_with($name, 'en.')) {
            return $name;
        }

        // Locale-agnostic routes that should never get an `en.` prefix —
        // they live outside the localized route group by design.
        $agnostic = [
            'front.locale.switch',
            'midtrans.notification',
        ];
        if (in_array($name, $agnostic, true)) {
            return $name;
        }

        return 'en.'.$name;
    }
}
