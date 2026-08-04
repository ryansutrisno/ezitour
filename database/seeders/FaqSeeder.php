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
                'question' => ['id' => 'Bagaimana cara melakukan pembayaran?', 'en' => 'How can I make a payment?'],
                'answer' => ['id' => 'Pembayaran dilakukan melalui Midtrans Snap. Setelah memilih paket dan mengisi data, kamu bisa membayar menggunakan transfer bank, kartu kredit/debit, e-wallet (GoPay, OVO, DANA, ShopeePay), atau QRIS. Konfirmasi pembayaran terjadi otomatis dan instan.', 'en' => 'Payments are made through Midtrans Snap. After choosing a package and entering your details, you can pay by bank transfer, credit or debit card, e-wallet (GoPay, OVO, DANA, ShopeePay), or QRIS. Payment confirmation is automatic and instant.'],
                'category' => 'Pembayaran',
                'sort_order' => 1,
            ],
            [
                'question' => ['id' => 'Apakah ada biaya tersembunyi?', 'en' => 'Are there any hidden fees?'],
                'answer' => ['id' => 'Tidak ada. Harga yang tertera pada setiap paket sudah mencakup transportasi, supir berpengalaman, dan tiket masuk destinasi. Kamu membayar persis angka yang tertulis — tanpa kejutan biaya tambahan di lapangan.', 'en' => 'No. Every package price includes transportation, an experienced driver, and entrance tickets to the destinations. You pay exactly the listed price — with no surprise fees on the trip.'],
                'category' => 'Pembayaran',
                'sort_order' => 2,
            ],
            [
                'question' => ['id' => 'Apa yang termasuk dalam paket?', 'en' => 'What is included in the package?'],
                'answer' => ['id' => 'Setiap paket standar mencakup kendaraan dengan supir berpengalaman, tiket masuk ke seluruh destinasi dalam itinerary, dan koordinasi lengkap selama perjalanan. Detail spesifik tiap paket tertera pada halaman paket masing-masing.', 'en' => 'Every standard package includes a vehicle with an experienced driver, entrance tickets to all destinations in the itinerary, and full coordination throughout the trip. Specific details are listed on each package page.'],
                'category' => 'Paket',
                'sort_order' => 3,
            ],
            [
                'question' => ['id' => 'Apakah saya bisa memilih supir atau mobil sendiri?', 'en' => 'Can I choose my own driver or car?'],
                'answer' => ['id' => 'Penunjukan supir dan kendaraan dilakukan oleh tim operasional kami berdasarkan ketersediaan dan rute, supaya standar keselamatan dan kenyamanan tetap terjaga untuk semua traveler. Permintaan khusus bisa disampaikan dan akan kami usahakan semaksimal mungkin.', 'en' => 'Our operations team assigns the driver and vehicle based on availability and the route to maintain safety and comfort standards for every traveler. You can share special requests and we will do our best to accommodate them.'],
                'category' => 'Perjalanan',
                'sort_order' => 4,
            ],
            [
                'question' => ['id' => 'Bagaimana jika perjalanan dibatalkan?', 'en' => 'What if the trip is cancelled?'],
                'answer' => ['id' => 'Bila kamu membatalkan pesanan, hubungi tim support sesegera mungkin. Kebijakan refund bergantung pada jarak waktu pembatalan terhadap tanggal keberangkatan. Pembatalan dari sisi kami (khusus) akan diganti penuh.', 'en' => 'If you cancel your booking, contact our support team as soon as possible. The refund policy depends on how far the cancellation is from the departure date. Cancellations initiated by us will be fully refunded.'],
                'category' => 'Pembatalan',
                'sort_order' => 5,
            ],
            [
                'question' => ['id' => 'Bagaimana cara menghubungi support?', 'en' => 'How can I contact support?'],
                'answer' => ['id' => 'Tim support kami siap membantu 24/7 melalui WhatsApp dan email yang tertera di footer. Untuk kendala darurat saat perjalanan sedang berlangsung, hubungi kami lewat WhatsApp agar mendapat respon paling cepat.', 'en' => 'Our support team is available 24/7 through WhatsApp and the email listed in the footer. For emergencies during your trip, contact us via WhatsApp for the fastest response.'],
                'category' => 'Umum',
                'sort_order' => 6,
            ],
            [
                'question' => ['id' => 'Apakah saya bisa memesan paket untuk grup besar?', 'en' => 'Can I book a package for a large group?'],
                'answer' => ['id' => 'Bisa. Untuk grup besar, sekolah, atau perusahaan, silakan hubungi tim support kami untuk mendapatkan penawaran khusus dan kendaraan yang sesuai dengan jumlah peserta.', 'en' => 'Yes. For large groups, schools, or companies, contact our support team for a special offer and a vehicle suited to your group size.'],
                'category' => 'Paket',
                'sort_order' => 7,
            ],
            [
                'question' => ['id' => 'Apakah itinerary bisa disesuaikan?', 'en' => 'Can the itinerary be customized?'],
                'answer' => ['id' => 'Sebagian paket mendukung kustomisasi itinerary. Sampaikan preferensimu saat menghubungi support, dan kami akan bantu menyusun rute yang paling sesuai dengan kebutuhan dan anggaranmu.', 'en' => 'Some packages support itinerary customization. Share your preferences when contacting support, and we will help create a route that best suits your needs and budget.'],
                'category' => 'Perjalanan',
                'sort_order' => 8,
            ],
        ];

        foreach ($rows as $row) {
            Faq::updateOrCreate(['question->id' => $row['question']['id']], $row);
        }
    }
}
