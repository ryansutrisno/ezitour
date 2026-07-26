@component('mail::message')
# Pembayaran Berhasil! 🎉

Halo {{ $booking->user->name }}, pembayaranmu telah kami terima dan dikonfirmasi. Paket wisatamu siap dinikmati!

@component('mail::panel')
**E-Ticket**
- Kode Booking: **{{ $bookingCode }}**
- Paket: {{ $booking->package->name }}
- Tanggal Perjalanan: {{ $booking->travel_date->format('d/m/Y') }}
- Metode Pembayaran: {{ $transaction->payment_type ?? 'Midtrans' }}
- Total Dibayar: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- Status: **LUNAS** ✅
@endcomponent

Simpan email ini sebagai bukti pemesanan. Tim kami akan menghubungi kamu sebelum tanggal keberangkatan untuk detail antar-jemput.

@component('mail::button', ['url' => route('dashboard.index')])
Lihat Detail Pesanan
@endcomponent

Sampai jumpa di perjalanan!

Salam hangat,
Tim EziTour
@endcomponent
