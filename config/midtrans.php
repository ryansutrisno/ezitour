<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Server key digunakan untuk autentikasi dengan Midtrans API.
    | Dapatkan dari Midtrans Dashboard: Settings > Access Keys
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Client key digunakan untuk Snap.js di frontend.
    | Dapatkan dari Midtrans Dashboard: Settings > Access Keys
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set true untuk production environment, false untuk sandbox/testing.
    | Sandbox: untuk testing dengan data dummy
    | Production: untuk transaksi real dengan uang sungguhan
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitized Mode
    |--------------------------------------------------------------------------
    |
    | Enable sanitization untuk input data yang dikirim ke Midtrans.
    | Recommended: true untuk keamanan
    |
    */
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    /*
    |--------------------------------------------------------------------------
    | 3DS Mode
    |--------------------------------------------------------------------------
    |
    | Enable 3D Secure untuk credit card transactions.
    | Recommended: true untuk keamanan tambahan
    |
    */
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Expiry Duration
    |--------------------------------------------------------------------------
    |
    | Durasi waktu kadaluarsa pembayaran dalam menit.
    | Default: 1440 menit (24 jam)
    | Minimum: 5 menit
    | Maximum: 10080 menit (7 hari)
    |
    */
    'expiry_duration' => env('MIDTRANS_EXPIRY_DURATION', 1440),

];
