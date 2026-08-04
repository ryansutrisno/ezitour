<?php

namespace App\Providers;

use App\Routing\UrlGenerator as AppUrlGenerator;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Phase 1 i18n: swap in our locale-aware URL generator so blade
        // templates can call route('front.home') and get either '/' (ID)
        // or '/en/' (EN) based on the active app locale. See
        // App\Routing\UrlGenerator for the namespace-switching logic.
        $this->app->extend(BaseUrlGenerator::class, function (BaseUrlGenerator $base, $app): BaseUrlGenerator {
            $custom = new AppUrlGenerator(
                $app->make('router')->getRoutes(),
                $app->make('request'),
            );

            // Mirror Laravel's resolvers from the base generator so session
            // backing, signing, and asset URL handling still work.
            $custom->setSessionResolver(function () use ($app) {
                if ($app->bound('session')) {
                    return $app->make('session.store');
                }

                return null;
            });
            $custom->setKeyResolver(function () use ($app) {
                return $app->make('config')->get('app.key');
            });

            return $custom;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Translatable::fallback(config('app.fallback_locale'), true);
    }
}
