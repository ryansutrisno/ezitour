<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $siteName;

    public string $tagline;

    public string $footerTagline;

    public ?string $footerTagline_en = null;

    public static function group(): string
    {
        return 'general';
    }
}
