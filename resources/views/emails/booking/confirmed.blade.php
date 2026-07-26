@component('mail::message')
# Halo {{ $booking->user->name }}!

Terima kasih telah memesan paket wisata di EziTour. Pesananmu telah kami terima dan sedang menunggu pembayaran.

@component('mail::panel')
**Detail Pesanan**
- Kode Booking: **{{ $bookingCode }}**
- Paket: {{ $booking->package->name }}
- Tanggal Perjalanan: {{ $booking->travel_date->format('d/m/Y') }}
- Total Pembayaran: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- Status: Menunggu Pembayaran
@endcomponent

Selesaikan pembayaran dalam 24 jam untuk menghindari pembatalan otomatis.

@component('mail::button', ['url' => route('dashboard.index')])
Lihat Pesanan Saya
@endcomponent

Terima kasih telah mempercayakan liburanmu kepada kami.

Salam hangat,
Tim EziTour
@endcomponent
