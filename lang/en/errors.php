<?php

/*
|--------------------------------------------------------------------------
| Error page strings (English)
|--------------------------------------------------------------------------
| Natural tone mirroring the Indonesian errors.php.
*/

return [

    // Generic Ocean layout defaults
    'default_headline' => 'Something went wrong',
    'default_description' => 'Please try again later.',
    'back_home_button' => 'Back to Home',
    'footer_note' => 'EziTour. Crafted with love in Indonesia.',

    // 404
    '404_headline' => 'Page not found',
    '404_description' => "Looks like you're lost. The page you're looking for doesn't exist or has been moved.",

    // 403
    '403_headline' => 'Access denied',
    '403_description' => "You don't have permission to access this page.",

    // 419 (CSRF / session expired)
    '419_headline' => 'Session expired',
    '419_description' => 'This page has expired. Please reload and try again.',

    // 500
    '500_headline' => 'Something went wrong',
    '500_description' => 'Our servers are having trouble. Our team has been notified — please try again later.',

    // 503
    '503_headline' => 'Under maintenance',
    '503_description' => "We're doing some upgrades to serve you better. Please try again in a few minutes.",
];
