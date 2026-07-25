@extends('layouts.front')

@section('title', 'FAQ - EziTour')

@section('content')
    @php($contact = app(App\Settings\ContactSettings::class))

    {{-- ============================================================
    (1) HERO — gradient mesh, konsisten dengan home/about
    ============================================================ --}}
    <section class="relative overflow-hidden bg-slate-50">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -right-24 w-[40rem] h-[40rem] rounded-full bg-blue-200/40 blur-3xl"></div>
            <div class="absolute top-40 -left-32 w-[34rem] h-[34rem] rounded-full bg-sand-200/40 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(15_23_42/0.05)_1px,transparent_0)] [background-size:22px_22px]"></div>
        </div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24 text-center">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-pill bg-white shadow-soft text-blue-700 text-xs font-semibold border border-blue-100">
                <span class="flex h-2 w-2 rounded-full bg-sand-500"></span>
                Pusat Bantuan
            </span>
            <h1 class="mt-6 font-display font-extrabold tracking-tight text-slate-900 text-4xl sm:text-5xl lg:text-6xl leading-[1.05]">
                Pertanyaan yang
                <span class="block bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">sering ditanyakan.</span>
            </h1>
            <p class="mt-6 mx-auto max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed">
                Temukan jawaban cepat soal pembayaran, perjalanan, paket, dan kebijakan pembatalan. Kalau tidak menemukan yang kamu cari, tim support kami siap membantu.
            </p>
        </div>
    </section>

    {{-- ============================================================
    (2) FAQ LIST — grouped accordion (vanilla <details>/<summary>)
    ============================================================ --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach($grouped as $category => $categoryFaqs)
                <div class="mb-12 last:mb-0">
                    <h2 class="font-display text-xl font-bold tracking-tight text-slate-900 mb-5 flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-button bg-blue-50 text-blue-600 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        {{ $category }}
                    </h2>

                    <div class="space-y-3">
                        @foreach($categoryFaqs as $faq)
                            <details class="group rounded-card border border-slate-100 bg-white shadow-soft hover:shadow-card transition-shadow">
                                <summary class="flex items-center justify-between gap-4 cursor-pointer p-5 font-display font-bold text-slate-900 list-none [&::-webkit-details-marker]:hidden">
                                    <span>{{ $faq->question }}</span>
                                    <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </summary>
                                <div class="px-5 pb-5 -mt-1 text-slate-600 leading-relaxed">
                                    {{ $faq->answer }}
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($faqs->isEmpty())
                <p class="text-center text-slate-500 py-12">Belum ada pertanyaan yang ditampilkan saat ini.</p>
            @endif
        </div>
    </section>

    {{-- ============================================================
    (3) CTA — still have questions? contact support
    ============================================================ --}}
    <section class="pb-16 sm:pb-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-blue-700 via-blue-700 to-blue-800 px-8 py-12 sm:px-12 sm:py-14 shadow-hover text-center">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-blue-500/30 blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-sand-400/20 blur-3xl"></div>
                </div>
                <div class="relative max-w-2xl mx-auto">
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Masih ada pertanyaan?</h2>
                    <p class="mt-3 text-blue-100 leading-relaxed">Tim support kami siap membantu 24/7. Hubungi kami lewat WhatsApp atau email, kami akan segera membalas.</p>
                    <div class="mt-7 flex flex-col sm:flex-row justify-center gap-3">
                        @if($contact->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-bold text-blue-700 bg-white hover:bg-sand-50 shadow-soft transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Chat WhatsApp
                            </a>
                        @endif
                        <a href="mailto:{{ $contact->email }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $contact->email }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
