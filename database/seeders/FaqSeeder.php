<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Seed common travel FAQs (Indonesian) covering the main categories:
     * Pembayaran, Perjalanan, Pembatalan, Paket, Umum.
     */
    public function run(): void
    {
        $rows = [
            [
                'question' => 'Bagaimana cara melakukan pembayaran?',
                'answer' => 'Pembayaran dilakukan melalui Midtrans Snap. Setelah memilih paket dan mengisi data, kamu bisa membayar menggunakan transfer bank, kartu kredit/debit, e-wallet (GoPay, OVO, DANA, ShopeePay), atau QRIS. Konfirmasi pembayaran terjadi otomatis dan instan.',
                'category' => 'Pembayaran',
                'sort_order' => 1,
            ],
            [
                'question' => 'Apakah ada biaya tersembunyi?',
                'answer' => 'Tidak ada. Harga yang tertera pada setiap paket sudah mencakup transportasi, supir berpengalaman, dan tiket masuk destinasi. Kamu membayar persis angka yang tertulis — tanpa kejutan biaya tambahan di lapangan.',
                'category' => 'Pembayaran',
                'sort_order' => 2,
            ],
            [
                'question' => 'Apa yang termasuk dalam paket?',
                'answer' => 'Setiap paket standar mencakup kendaraan dengan supir berpengalaman, tiket masuk ke seluruh destinasi dalam itinerary, dan koordinasi lengkap selama perjalanan. Detail spesifik tiap paket tertera pada halaman paket masing-masing.',
                'category' => 'Paket',
                'sort_order' => 3,
            ],
            [
                'question' => 'Apakah saya bisa memilih supir atau mobil sendiri?',
                'answer' => 'Penunjukan supir dan kendaraan dilakukan oleh tim operasional kami berdasarkan ketersediaan dan rute, supaya standar keselamatan dan kenyamanan tetap terjaga untuk semua traveler. Permintaan khusus bisa disampaikan dan akan kami usahakan semaksimal mungkin.',
                'category' => 'Perjalanan',
                'sort_order' => 4,
            ],
            [
                'question' => 'Bagaimana jika perjalanan dibatalkan?',
                'answer' => 'Bila kamu membatalkan pesanan, hubungi tim support sesegera mungkin. Kebijakan refund bergantung pada jarak waktu pembatalan terhadap tanggal keberangkatan. Pembatalan dari sisi kami (khusus) akan diganti penuh.',
                'category' => 'Pembatalan',
                'sort_order' => 5,
            ],
            [
                'question' => 'Bagaimana cara menghubungi support?',
                'answer' => 'Tim support kami siap membantu 24/7 melalui WhatsApp dan email yang tertera di footer. Untuk kendala darurat saat perjalanan sedang berlangsung, hubungi kami lewat WhatsApp agar mendapat respon paling cepat.',
                'category' => 'Umum',
                'sort_order' => 6,
            ],
            [
                'question' => 'Apakah saya bisa memesan paket untuk grup besar?',
                'answer' => 'Bisa. Untuk grup besar, sekolah, atau perusahaan, silakan hubungi tim support kami untuk mendapatkan penawaran khusus dan kendaraan yang sesuai dengan jumlah peserta.',
                'category' => 'Paket',
                'sort_order' => 7,
            ],
            [
                'question' => 'Apakah itinerary bisa disesuaikan?',
                'answer' => 'Sebagian paket mendukung kustomisasi itinerary. Sampaikan preferensimu saat menghubungi support, dan kami akan bantu menyusun rute yang paling sesuai dengan kebutuhan dan anggaranmu.',
                'category' => 'Perjalanan',
                'sort_order' => 8,
            ],
        ];

        foreach ($rows as $row) {
            Faq::updateOrCreate(['question' => $row['question']], $row);
        }
    }
}
