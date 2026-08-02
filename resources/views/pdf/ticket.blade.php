{{--
    E-Ticket PDF (rendered by barryvdh/laravel-dompdf).

    dompdf has limited CSS support, so this view uses a table-based layout with
    inline styles only (no Tailwind, no CSS gradients, no web fonts). The Ocean
    & Sand palette is applied as solid hex colours and the built-in Helvetica
    core font renders Indonesian (Latin) text reliably.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Ticket {{ $booking->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 13px; line-height: 1.5; }
        .page { padding: 0; }
        .header { background-color: #1f6fe0; color: #ffffff; padding: 28px 36px; }
        .brand-row { width: 100%; }
        .brand-logo { font-size: 22px; font-weight: bold; letter-spacing: -0.5px; }
        .brand-logo .accent { color: #fde68a; }
        .ticket-label { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #bfdbfe; margin-top: 4px; }
        .headline { font-size: 26px; font-weight: bold; margin-top: 14px; }

        .code-strip { background-color: #f8fafc; border-bottom: 3px solid #e08a3c; padding: 18px 36px; }
        .code-label { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #64748b; }
        .code-value { font-size: 22px; font-weight: bold; color: #1759b5; letter-spacing: 1px; }

        .body { padding: 28px 36px; }
        .section-title { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.info td { padding: 8px 0; vertical-align: top; font-size: 13px; }
        table.info td.label { color: #64748b; width: 38%; }
        table.info td.value { color: #0f172a; font-weight: bold; }

        .total-row { background-color: #f1f5f9; border-radius: 8px; padding: 14px 18px; margin-top: 8px; }
        .total-row .label { color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .total-row .amount { font-size: 22px; font-weight: bold; color: #1759b5; }

        .status-badge { display: inline-block; padding: 6px 14px; background-color: #16a34a; color: #ffffff; font-weight: bold; font-size: 11px; letter-spacing: 1px; border-radius: 4px; }

        .footer { margin-top: 30px; padding: 20px 36px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 11px; color: #64748b; margin-bottom: 4px; }
        .footer .thank { font-size: 13px; color: #0f172a; font-weight: bold; margin-bottom: 8px; }

        .package-box { padding: 14px 16px; background-color: #eff6ff; border-left: 4px solid #1f6fe0; margin-bottom: 20px; }
        .package-box .pkg-name { font-size: 16px; font-weight: bold; color: #0f172a; }
        .package-box .pkg-sub { font-size: 12px; color: #475569; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="page">
        {{-- Header band --}}
        <div class="header">
            <table class="brand-row"><tr>
                <td>
                    <div class="brand-logo">Ezi<span class="accent">Tour</span></div>
                    <div class="ticket-label">E-Ticket Resmi</div>
                </td>
                <td align="right">
                    <div class="status-badge">LUNAS</div>
                </td>
            </tr></table>
            <div class="headline">Selamat datang di petualanganmu!</div>
        </div>

        {{-- Code strip --}}
        <div class="code-strip">
            <table width="100%"><tr>
                <td>
                    <div class="code-label">Kode Booking</div>
                    <div class="code-value">{{ $booking->code }}</div>
                </td>
                <td align="right">
                    <div class="code-label">Tanggal Terbit</div>
                    <div class="code-value" style="font-size:14px;">{{ now()->format('d/m/Y') }}</div>
                </td>
            </tr></table>
        </div>

        <div class="body">
            {{-- Package --}}
            <div class="package-box">
                <div class="pkg-name">{{ $booking->package->name }}</div>
                <div class="pkg-sub">Paket Wisata EziTour</div>
            </div>

            {{-- Detail pesanan --}}
            <div class="section-title">Detail Pesanan</div>
            <table class="info">
                <tr>
                    <td class="label">Nama Traveler</td>
                    <td class="value">{{ $booking->user->name }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $booking->user->email }}</td>
                </tr>
                @if($booking->user->phone)
                <tr>
                    <td class="label">Nomor Telepon</td>
                    <td class="value">{{ $booking->user->phone }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Tanggal Perjalanan</td>
                    <td class="value">{{ $booking->travel_date->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Lokasi Penjemputan</td>
                    <td class="value">{{ $booking->pickup_location }}</td>
                </tr>
                @if($booking->latestTransaction && $booking->latestTransaction->payment_type)
                <tr>
                    <td class="label">Metode Pembayaran</td>
                    <td class="value">{{ ucwords(str_replace('_', ' ', $booking->latestTransaction->payment_type)) }}</td>
                </tr>
                @endif
            </table>

            {{-- Total --}}
            <div class="total-row">
                <table width="100%"><tr>
                    <td><span class="label">Total Dibayar</span></td>
                    <td align="right"><span class="amount">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</span></td>
                </tr></table>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="thank">Terima kasih telah mempercayakan liburanmu kepada EziTour!</div>
            <p>Simpan E-Ticket ini sebagai bukti pemesanan. Tim kami akan menghubungi kamu sebelum tanggal keberangkatan untuk detail antar-jemput.</p>
            <p style="margin-top:8px;">Butuh bantuan? Hubungi tim support kami di {{ config('app.name', 'EziTour') }}.</p>
        </div>
    </div>
</body>
</html>
