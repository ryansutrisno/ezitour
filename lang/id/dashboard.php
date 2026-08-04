<?php

/*
|--------------------------------------------------------------------------
| Dashboard strings (Indonesian — default locale)
|--------------------------------------------------------------------------
| Verbatim copy from dashboard/index + dashboard/partials/booking-card +
| bookings/show + payments/pay pre-i18n.
*/

return [

    // Dashboard page
    'title' => 'Dashboard Saya',
    'seo_title' => 'Dashboard Saya',
    'greeting' => 'Halo, :name! 👋',
    'welcome' => 'Selamat datang di dashboard perjalananmu.',
    'logout_button' => 'Keluar',
    'orders_section_title' => 'Riwayat Pesanan',
    'filter_all' => 'Semua',
    'filter_paid' => 'Lunas',
    'filter_unpaid' => 'Belum Lunas',
    'retry_button' => 'Coba Lagi',

    // Empty states
    'empty_paid_title' => 'Belum ada pesanan yang lunas',
    'empty_paid_body' => 'Pesanan yang sudah dibayar penuh akan muncul di sini.',
    'empty_unpaid_title' => 'Tidak ada pesanan yang belum lunas',
    'empty_unpaid_body' => 'Semua pesananmu sudah dibayar. Mantap! 🎉',
    'empty_all_title' => 'Belum ada pesanan',
    'empty_all_body' => 'Mulai petualanganmu dengan memesan paket wisata pertama.',
    'empty_cta' => 'Cari Paket Wisata',

    // Booking card labels (Sprint 5 + 6)
    'booking_id' => 'ID Booking: #:id',
    'status_paid' => 'Lunas',
    'status_pending_payment' => 'Menunggu Bayar',
    'status_pending_payment_short' => 'Menunggu',
    'status_failed' => 'Gagal',
    'status_unpaid' => 'Belum Dibayar',
    'status_completed' => 'Selesai',
    'paid_at' => 'Dibayar :date',
    'time_remaining' => 'Sisa :time',
    'need_help' => 'Butuh bantuan? Hubungi 24/7',
    'view_details' => 'Lihat Detail',
    'pay_now' => 'Bayar Sekarang',
    'continue_payment' => 'Lanjutkan Pembayaran',
    'discount_percent' => 'Hemat :percent%',

    // Booking detail page (bookings/show)
    'detail_title' => 'Detail Booking #:code',
    'detail_breadcrumb' => 'Detail Booking #:code',
    'booking_code' => 'Kode Booking',
    'status_pending' => 'Menunggu Pembayaran',
    'status_cancelled' => 'Dibatalkan',
    'status_unknown' => 'Unknown',
    'package_section_title' => 'Detail Paket',
    'traveler_section_title' => 'Data Traveler',
    'transactions_section_title' => 'Riwayat Pembayaran',
    'transactions_empty' => 'Belum ada transaksi pembayaran.',
    'summary_section_title' => 'Ringkasan',
    'summary_status' => 'Status',
    'summary_travel_date' => 'Tanggal Perjalanan',
    'summary_subtotal' => 'Subtotal',
    'summary_tier_discount' => 'Diskon Rombongan',
    'summary_coupon_discount' => 'Diskon Promo',
    'summary_total' => 'Total',
    'payment_method_label' => 'Metode',
    'driver_label' => 'Driver',
    'car_label' => 'Mobil',
    'total_label' => 'Total',
    'promo_badge' => 'Promo',
    'transaction_pending_method' => 'Menunggu metode',
    'cancel_confirm' => 'Yakin ingin membatalkan booking ini? Tindakan ini tidak dapat dibatalkan.',
    'cancel_button' => 'Batalkan Booking',
    'retry_payment_button' => 'Coba Bayar Lagi',
    'download_ticket_button' => 'Download E-Ticket',
    'contact_support_button' => 'Hubungi Support',
    'cancelled_notice' => 'Booking ini telah dibatalkan.',
    'view_other_packages' => 'Lihat Paket Lain',
    'traveler_name' => 'Nama',
    'traveler_email' => 'Email',
    'traveler_phone' => 'Telepon',
    'txn_status_pending' => 'Menunggu',
    'txn_status_paid' => 'Lunas',
    'txn_status_failed' => 'Gagal',
    'txn_status_expired' => 'Kedaluwarsa',
    'txn_status_superseded' => 'Digantikan',

    // Payments pay page
    'payment_title' => 'Pembayaran',
    'payment_subtitle_retry' => 'Silakan coba pembayaran kembali',
    'payment_subtitle_default' => 'Selesaikan pembayaran untuk booking Anda',
    'payment_summary_title' => 'Ringkasan Booking',
    'payment_order_id' => 'Order ID',
    'payment_package' => 'Paket Wisata',
    'payment_travel_date' => 'Tanggal Perjalanan',
    'payment_customer_name' => 'Nama Pemesan',
    'payment_total' => 'Total Pembayaran',
    'payment_info_title' => 'Informasi Pembayaran',
    'payment_info_step1' => 'Klik "Bayar Sekarang" untuk memilih metode pembayaran',
    'payment_info_step2' => 'Bayar dengan kartu kredit, transfer bank, atau e-wallet',
    'payment_info_step3' => 'Diproses secara aman melalui Midtrans',
    'payment_button_hint' => 'Klik tombol di atas untuk memilih metode pembayaran',
    'payment_trust_badge' => 'Pembayaran aman dengan Midtrans',
];
