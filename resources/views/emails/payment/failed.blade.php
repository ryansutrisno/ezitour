@component('mail::message')
# Pembayaran Tidak Berhasil

Halo {{ $booking->user->name }}, sepertinya pembayaranmu tidak dapat kami proses.

@component('mail::panel')
**Detail Pesanan**
- Kode Booking: **{{ $bookingCode }}**
- Paket: {{ $booking->package->name }}
- Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- Status: Gagal
@endcomponent

Jangan khawatir — pesananmu masih aktif. Kamu bisa mencoba pembayaran kembali kapan saja dari dashboard.

@component('mail::button', ['url' => route('dashboard.index')])
Lihat & Coba Bayar Lagi
@endcomponent

Butuh bantuan? Balas email ini atau hubungi tim support kami.

Salam hangat,
Tim EziTour
@endcomponent
