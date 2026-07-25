<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $heroBadge;

    public string $heroHeadline;

    public string $heroHeadlineAccent;

    public string $heroSubheadline;

    public string $statDestinations;

    public string $statTravelers;

    public string $statRating;

    public string $statSupport;

    public static function group(): string
    {
        return 'home';
    }
}
