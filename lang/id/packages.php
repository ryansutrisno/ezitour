<?php

/*
|--------------------------------------------------------------------------
| Package page strings (Indonesian — default locale)
|--------------------------------------------------------------------------
| Verbatim copy from packages/index, packages/show, checkout, and related
| partials pre-i18n. Includes facet filter labels (Sprint 3), booking card
| (Sprint 5), and reviews (Sprint 4).
*/

return [

    // Packages index — header & search
    'index_title' => 'Cari Paket Wisata',
    'index_seo_title' => 'Paket Wisata',
    'index_seo_description' => 'Jelajahi paket wisata terbaik di Indonesia bersama EziTour. Temukan liburan impianmu sekarang.',
    'index_eyebrow' => 'Jelajahi',
    'index_title_main' => 'Temukan Petualanganmu',
    'index_intro' => 'Pilih dari beragam paket wisata menarik yang kami siapkan untuk pengalaman tak terlupakan.',
    'search_placeholder' => 'Cari paket atau destinasi...',

    // Facet sidebar (Sprint 3)
    'filter_title' => 'Filter Paket',
    'filter_region' => 'Wilayah',
    'filter_category' => 'Kategori',
    'filter_duration' => 'Durasi',
    'filter_active' => 'Filter Aktif',
    'filter_clear' => 'Bersihkan',
    'filter_clear_all' => 'Hapus Filter',
    'filter_all' => 'Semua',
    'filter_no_regions' => 'Belum ada wilayah terdaftar.',
    'filter_no_categories' => 'Belum ada kategori terdaftar.',

    // Results
    'no_results_title' => 'Tidak ada paket yang cocok',
    'no_results_body' => 'Maaf, tidak ada paket yang cocok dengan pencarianmu. Coba ubah atau hapus filter.',
    'no_results_cta' => 'Lihat semua paket',
    'price_from' => 'Mulai dari',
    'result_detail' => 'Detail',
    'duration_days' => ':days hari',

    // Package detail (show)
    'show_breadcrumb_home' => 'Home',
    'show_breadcrumb_packages' => 'Paket Wisata',
    'show_itinerary_title' => 'Itinerary Perjalanan',
    'show_facilities_title' => 'Fasilitas Termasuk',
    'show_facility_car' => 'Mobil Ber-AC (Avanza/Innova/Hiace)',
    'show_facility_driver' => 'Supir Berpengalaman + BBM',
    'show_facility_ticket' => 'Tiket Masuk Wisata (Sesuai Itinerary)',
    'show_facility_water' => 'Air Mineral',
    'show_booking_card_title' => 'Mulai Liburanmu!',
    'show_duration_label' => 'Durasi:',
    'show_destinations_count' => ':count destinasi wisata',
    'show_secure_note' => 'Pembayaran aman & terpercaya via Midtrans',
    'show_facilities_included' => 'Fasilitas Termasuk',

    // Reviews section (Sprint 4)
    'reviews_title' => 'Ulasan Pelanggan',
    'reviews_summary' => ':avg dari 5 (:count ulasan)',
    'reviews_empty' => 'Belum ada ulasan untuk paket ini.',
    'reviews_login_prompt_prefix' => '',
    'reviews_login_prompt_link' => 'Masuk',
    'reviews_login_prompt_suffix' => 'untuk meninggalkan ulasan.',
    'reviews_already_reviewed' => 'Terima kasih, Anda sudah memberikan ulasan untuk paket ini.',
    'reviews_no_paid_booking' => 'Pesan dan selesaikan perjalanan ini untuk memberikan ulasan.',
    'reviews_form_title' => 'Tulis Ulasan Anda',
    'reviews_form_rating_label' => 'Rating',
    'reviews_form_stars_suffix' => 'bintang',
    'reviews_form_comment_label' => 'Komentar',
    'reviews_form_comment_placeholder' => 'Bagikan pengalaman Anda...',
    'reviews_form_submit' => 'Kirim Ulasan',

    // Checkout (Sprint 5 + 6)
    'checkout_title' => 'Checkout',
    'checkout_breadcrumb_home' => 'Home',
    'checkout_breadcrumb_packages' => 'Paket Wisata',
    'checkout_booking_form_title' => 'Detail Pemesanan',
    'checkout_travel_date_label' => 'Tanggal Perjalanan',
    'checkout_participants_label' => 'Jumlah Peserta',
    'checkout_participants_hint' => 'Maksimal 50 peserta per pemesanan',
    'checkout_pickup_label' => 'Lokasi Penjemputan',
    'checkout_pickup_placeholder' => 'Contoh: Hotel Tentrem Yogyakarta, Jl. AM Sangaji No.72A...',
    'checkout_price_breakdown_title' => 'Rincian Harga',
    'checkout_price_per_pax' => 'Harga per orang',
    'checkout_participants_count' => 'Jumlah peserta',
    'checkout_total' => 'Total Harga',
    'checkout_summary_title' => 'Ringkasan Pesanan',
    'checkout_destinations_label' => 'Destinasi Wisata',
    'checkout_more_destinations' => '+:count destinasi lainnya',
    'checkout_coupon_label' => 'Kode Promo',
    'checkout_coupon_placeholder' => 'Contoh: LIBURAN50',
    'checkout_coupon_remove' => 'Hapus',
    'checkout_tier_discount' => 'Diskon Rombongan',
    'checkout_coupon_discount' => 'Diskon Promo',
    'checkout_summary_total' => 'Total Pembayaran',
    'checkout_continue_auth' => 'Lanjutkan →',
    'checkout_continue_payment' => 'Lanjutkan ke Pembayaran →',
    'checkout_guest_hint' => 'Anda akan diminta untuk login atau mendaftar setelah ini',
    'checkout_auth_section_title' => 'Login atau Daftar',
    'checkout_auth_login_tab' => 'Login',
    'checkout_auth_register_tab' => 'Daftar Baru',
    'checkout_auth_login_submit' => 'Login & Lanjutkan →',
    'checkout_auth_register_submit' => 'Daftar & Lanjutkan →',
    'checkout_auth_login_prompt' => 'Belum punya akun?',
    'checkout_auth_login_link' => 'Daftar sekarang',
    'checkout_auth_register_prompt' => 'Sudah punya akun?',
    'checkout_auth_register_link' => 'Login di sini',
    'checkout_password_mismatch' => 'Password dan konfirmasi tidak cocok',
    'checkout_back_to_package' => 'Kembali ke Detail Paket',
    'checkout_tier_badge' => 'Hemat :amount (:percent%) — :tier',

    // Coupon JS messages (Sprint 6)
    'coupon_applied' => '🎉 Promo :code berhasil! Hemat :discount',
    'coupon_invalid' => 'Promo tidak valid.',
    'coupon_validation_failed' => 'Gagal memvalidasi promo. Silakan coba lagi.',
];
