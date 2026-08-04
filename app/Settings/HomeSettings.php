<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $heroBadge;

    public string $heroHeadline;

    public string $heroHeadlineAccent;

    public string $heroSubheadline;

    public ?string $heroBadge_en = null;

    public ?string $heroHeadline_en = null;

    public ?string $heroHeadlineAccent_en = null;

    public ?string $heroSubheadline_en = null;

    public string $statDestinations;

    public string $statTravelers;

    public string $statRating;

    public string $statSupport;

    public static function group(): string
    {
        return 'home';
    }
}
