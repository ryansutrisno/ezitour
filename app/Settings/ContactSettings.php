<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string $email;

    public string $phone;

    public ?string $whatsapp;

    public ?string $address;

    public ?string $instagramUrl;

    public ?string $facebookUrl;

    public ?string $twitterUrl;

    public static function group(): string
    {
        return 'contact';
    }
}
