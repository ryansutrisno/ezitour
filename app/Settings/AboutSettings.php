<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public string $foundedYear;

    public string $provincesCovered;

    public string $partnersCount;

    public string $visionText;

    // List of mission points (each an array with a "point" key) produced by the
    // Filament Repeater. No `@var` docblock on purpose: spatie's
    // SettingsCastFactory parses docblock types and would try to apply a global
    // cast (e.g. the spatie/laravel-data one) to the array shape. Relying on
    // the native `array` type means no cast is resolved and the value simply
    // round-trips through the default JSON encoder.
    public array $missionPoints;

    public static function group(): string
    {
        return 'about';
    }
}
