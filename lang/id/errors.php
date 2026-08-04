<?php

/*
|--------------------------------------------------------------------------
| Error page strings (Indonesian — default locale)
|--------------------------------------------------------------------------
| Verbatim copy from errors/ocean.blade.php and the per-status child blades
| (404/403/419/500/503) pre-i18n. The Ocean error layout is self-contained
| (renders without Vite/compiled assets), so the strings here are read via
| __() from the @extends() variable arrays.
*/

return [

    // Generic Ocean layout defaults
    'default_headline' => 'Terjadi kesalahan',
    'default_description' => 'Silakan coba lagi nanti.',
    'back_home_button' => 'Kembali ke Beranda',
    'footer_note' => 'EziTour. Dibuat dengan rasa di Indonesia.',

    // 404
    '404_headline' => 'Halaman tidak ditemukan',
    '404_description' => 'Sepertinya kamu tersesat. Halaman yang kamu cari tidak ada atau sudah dipindahkan.',

    // 403
    '403_headline' => 'Akses ditolak',
    '403_description' => 'Kamu tidak punya izin untuk mengakses halaman ini.',

    // 419 (CSRF / session expired)
    '419_headline' => 'Sesi berakhir',
    '419_description' => 'Halaman ini sudah kedaluwarsa. Silakan muat ulang dan coba lagi.',

    // 500
    '500_headline' => 'Terjadi kesalahan',
    '500_description' => 'Server kami sedang mengalami masalah. Tim kami sudah diberi notifikasi — coba lagi nanti.',

    // 503
    '503_headline' => 'Sedang maintenance',
    '503_description' => 'Kami sedang melakukan perbaikan untuk pengalaman yang lebih baik. Coba lagi dalam beberapa menit.',
];
