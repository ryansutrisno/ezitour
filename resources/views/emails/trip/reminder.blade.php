@component('mail::message')
# Perjalanan Anda Besok! 🎒

Halo {{ $booking->user->name }}, bersiaplah — petualangan Anda di paket **{{ $booking->package->name }}** akan dimulai besok! 🌊

@component('mail::panel')
**Detail Perjalanan**
- Kode Booking: **{{ $bookingCode }}**
- Paket: {{ $booking->package->name }}
- Tanggal Perjalanan: {{ $booking->travel_date->format('d F Y') }}
- Titik Antar-Jemput: {{ $booking->pickup_location }}
- Total Pembayaran: Rp {{ number_format($totalAmount, 0, ',', '.') }}
- Status: **LUNAS** ✅
@endcomponent

Beberapa hal yang perlu Anda siapkan:
- Dokumen identitas (KTP/SIM/Paspor) sesuai kebutuhan perjalanan.
- Pakaian nyaman dan sesuai cuaca destinasi.
- Obat pribadi jika diperlukan.

Tim kami akan menghubungi Anda untuk konfirmasi jam antar-jemput. Pastikan nomor WhatsApp Anda aktif.

@component('mail::button', ['url' => route('bookings.show', $booking)])
Lihat Detail Booking
@endcomponent

Sampai jumpa di perjalanan!

Salam hangat,
Tim EziTour
@endcomponent
