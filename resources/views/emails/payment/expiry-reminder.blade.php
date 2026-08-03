@component('mail::message')
# Pembayaran Anda Segera Kedaluwarsa ⏰

Halo {{ $booking->user->name }}, pesananmu di EziTour akan segera **kedaluwarsa** jika pembayaran tidak diselesaikan segera. Jangan sampai kehilangan paket liburan impianmu!

@component('mail::panel')
**Detail Pesanan**
- Kode Booking: **{{ $bookingCode }}**
- Paket: {{ $booking->package->name }}
- Tanggal Perjalanan: {{ $booking->travel_date->format('d F Y') }}
- Total Pembayaran: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- Status: Menunggu Pembayaran
@endcomponent

Segera selesaikan pembayaran sebelum pesananmu dibatalkan secara otomatis dan kuota perjalananmu dilepaskan ke peserta lain.

@component('mail::button', ['url' => route('dashboard.index')])
Selesaikan Pembayaran
@endcomponent

Butuh bantuan? Balas email ini atau hubungi tim support kami.

Salam hangat,
Tim EziTour
@endcomponent
