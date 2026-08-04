@extends('layouts.front')

@section('title', $package->name . ' - EziTour')

@section('seo')
    <x-seo
        :title="$package->name"
        :description="$package->description"
        :image="$package->thumbnail_url"
        type="article"
        :canonical="route('front.packages.show', $package->slug)"
    />
@endsection

@section('content')
    @php
        $approvedReviews = $package->approvedReviews()->with('user')->get();
        $avgRating = $approvedReviews->isNotEmpty() ? round($approvedReviews->avg('rating'), 1) : null;

        // Determine the user's relationship to this package for the review form.
        $canReview = false;
        $alreadyReviewed = false;
        $hasPaidBooking = false;
        if (auth()->check()) {
            $alreadyReviewed = $package->reviews()->where('user_id', auth()->id())->exists();
            $hasPaidBooking = App\Models\Booking::query()
                ->where('user_id', auth()->id())
                ->where('package_id', $package->id)
                ->where('status', 'paid')
                ->exists();
            $canReview = $hasPaidBooking && ! $alreadyReviewed;
        }
    @endphp
    <div class="bg-white">
        {{-- Hero header --}}
        <div class="relative h-80 sm:h-96">
            @if($package->thumbnail_url)
                <img class="w-full h-full object-cover" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
            @else
                <div class="w-full h-full bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/30 flex items-end">
                <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-10">
                    <nav class="mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1.5 text-xs">
                            <li><a href="{{ route('front.home') }}" class="text-blue-100 hover:text-white transition-colors">{{ __('packages.show_breadcrumb_home') }}</a></li>
                            <li class="text-blue-200">/</li>
                            <li><a href="{{ route('front.packages.index') }}" class="text-blue-100 hover:text-white transition-colors">{{ __('packages.show_breadcrumb_packages') }}</a></li>
                        </ol>
                    </nav>
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">{{ $package->name }}</h1>
                    @if($package->description)
                        <p class="mt-3 text-base sm:text-lg text-slate-200 max-w-3xl leading-relaxed">{{ $package->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-12">
                {{-- Main Content: Itinerary --}}
                <div class="lg:col-span-2">
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 mb-8">{{ __('packages.show_itinerary_title') }}</h2>

                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($package->items as $index => $item)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-blue-100" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-4">
                                        <div>
                                            <span class="flex items-center justify-center h-10 w-10 rounded-pill bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display font-bold text-sm shadow-soft ring-4 ring-white">
                                                {{ $loop->iteration }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1 flex justify-between space-x-4 gap-4">
                                            <div>
                                                <h3 class="font-display text-lg font-bold text-slate-900">{{ $item->destination->name }}</h3>
                                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $item->destination->description }}</p>
                                                <span class="mt-2 inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-pill">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $item->destination->avg_duration }} {{ __('front.duration_minutes') }}
                                                </span>
                                            </div>
                                            <div class="shrink-0">
                                                @if($item->destination->image_url)
                                                    <img class="h-20 w-20 rounded-button object-cover" src="{{ $item->destination->image_url }}" alt="{{ $item->destination->name }}">
                                                @else
                                                    <div class="h-20 w-20 rounded-button bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Facilities --}}
                    <div class="mt-12 bg-gradient-to-br from-blue-50 to-slate-50 rounded-card p-6 sm:p-7 border border-blue-100">
                        <h3 class="font-display text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('packages.show_facilities_title') }}
                        </h3>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('packages.show_facility_car') }}
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('packages.show_facility_driver') }}
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('packages.show_facility_ticket') }}
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ __('packages.show_facility_water') }}
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Booking Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-card border border-slate-100 shadow-card p-6 sticky top-20">
                        <h3 class="font-display text-xl font-bold text-slate-900 mb-4">{{ __('packages.show_booking_card_title') }}</h3>

                        <div class="flex items-baseline mb-5">
                            <span class="font-display text-3xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                            <span class="ml-2 text-sm text-slate-500">{{ __('front.per_pax') }}</span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="h-5 w-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('packages.show_duration_label') }} {{ $package->items->sum(fn($item) => $item->destination->avg_duration) }} {{ __('front.duration_minutes') }}
                            </div>
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="h-5 w-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ __('packages.show_destinations_count', ['count' => $package->items->count()]) }}
                            </div>
                        </div>

                        <a href="{{ route('front.checkout.show', $package->slug) }}" class="w-full flex justify-center items-center gap-2 py-3.5 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                            {{ __('front.button_book_now') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        {{-- Wishlist heart toggle (full-width secondary action) --}}
                        <button type="button"
                            data-wishlist-toggle
                            data-package-slug="{{ $package->slug }}"
                            @class([
                                'mt-3 w-full inline-flex justify-center items-center gap-2 py-2.5 rounded-button text-sm font-semibold border transition-all',
                                'bg-red-50 border-red-200 text-red-600 hover:bg-red-100' => $package->is_favorited,
                                'bg-white border-slate-200 text-slate-700 hover:border-blue-400 hover:text-blue-600' => ! $package->is_favorited,
                            ])
                            title="{{ $package->is_favorited ? __('front.button_remove_wishlist') : __('front.button_save_wishlist') }}"
                            aria-label="{{ $package->is_favorited ? __('front.button_remove_wishlist') : __('front.button_save_wishlist') }}"
                            aria-pressed="{{ $package->is_favorited ? 'true' : 'false' }}">
                            @if($package->is_favorited)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                                {{ __('front.button_remove_wishlist') }}
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
                                {{ __('front.button_save_wishlist') }}
                            @endif
                        </button>

                        <p class="mt-4 text-xs text-center text-slate-400">{{ __('packages.show_secure_note') }}</p>
                    </div>
                </div>
            </div>

            {{-- ============ REVIEWS SECTION ============ --}}
            <div id="reviews" class="mt-14 scroll-mt-24">                <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                    {{-- Gradient header Ocean blue --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-9 h-9 rounded-button bg-white/15">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                            </span>
                            <div>
                                <h2 class="font-display text-xl font-bold text-white">{{ __('packages.reviews_title') }}</h2>
                                @if($avgRating !== null)
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-sand-300 text-sm">{{ str_repeat('★', (int) round($avgRating)) }}</span>
                                        <span class="text-blue-100 text-xs">{{ __('packages.reviews_summary', ['avg' => $avgRating, 'count' => $approvedReviews->count()]) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-7">
                        {{-- Review list --}}
                        @if($approvedReviews->isNotEmpty())
                            <div class="space-y-6">
                                @foreach($approvedReviews as $review)
                                    <div class="flex gap-4 {{ ! $loop->last ? 'pb-6 border-b border-slate-100' : '' }}">
                                        <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-pill bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display font-bold text-sm shadow-soft">
                                            {{ $review->user->initials }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-display font-bold text-slate-900">{{ $review->user->name }}</h3>
                                                <span class="text-sand-500 text-sm tracking-tight">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                                                <span class="text-xs text-slate-400">• {{ $review->created_at->translatedFormat('d M Y') }}</span>
                                            </div>
                                            @if($review->comment)
                                                <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-pill bg-slate-100 text-slate-400">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <p class="mt-3 text-sm text-slate-500">{{ __('packages.reviews_empty') }}</p>
                            </div>
                        @endif

                        {{-- Review form / conditional info cards --}}
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            @if(session('success'))
                                <div class="mb-5 rounded-button bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @guest
                                {{-- Guest: prompt to login --}}
                                <div class="rounded-button bg-blue-50 border border-blue-100 px-5 py-4 flex items-center gap-3">
                                    <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <div class="text-sm text-blue-800">
                                        <a href="{{ route('login') }}" class="font-semibold underline hover:text-blue-900">{{ __('packages.reviews_login_prompt_link') }}</a> {{ __('packages.reviews_login_prompt_suffix') }}
                                    </div>
                                </div>
                            @elseif($alreadyReviewed)
                                {{-- Already reviewed --}}
                                <div class="rounded-button bg-green-50 border border-green-100 px-5 py-4 flex items-center gap-3">
                                    <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <div class="text-sm text-green-700">{{ __('packages.reviews_already_reviewed') }}</div>
                                </div>
                            @elseif(! $hasPaidBooking)
                                {{-- Authed but no paid booking --}}
                                <div class="rounded-button bg-sand-50 border border-sand-200 px-5 py-4 flex items-center gap-3">
                                    <svg class="w-6 h-6 text-sand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div class="text-sm text-sand-700">{{ __('packages.reviews_no_paid_booking') }}</div>
                                </div>
                            @else
                                {{-- Can review: show form --}}
                                @if($errors->any())
                                    <div class="mb-4 rounded-button bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                                        {{ $errors->first() }}
                                    </div>
                                @endif
                                <form action="{{ route('front.reviews.store', $package->slug) }}" method="POST">
                                    @csrf
                                    <h3 class="font-display text-lg font-bold text-slate-900 mb-3">{{ __('packages.reviews_form_title') }}</h3>
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('packages.reviews_form_rating_label') }}</label>
                                        <div class="flex items-center gap-1.5">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" class="peer hidden" @if(old('rating') == $i) checked @endif>
                                                <label for="rating-{{ $i }}" class="cursor-pointer text-3xl text-slate-300 transition-colors hover:text-sand-400 peer-checked:text-sand-500" title="{{ $i }} {{ __('packages.reviews_form_stars_suffix') }}">★</label>
                                            @endfor
                                        </div>
                                        @error('rating')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="comment" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('packages.reviews_form_comment_label') }} <span class="text-slate-400 font-normal">{{ __('front.label_optional') }}</span></label>
                                        <textarea name="comment" id="comment" rows="4" maxlength="1000" class="block w-full rounded-input border border-slate-200 px-3.5 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none resize-y" placeholder="{{ __('packages.reviews_form_comment_placeholder') }}">{{ old('comment') }}</textarea>
                                        @error('comment')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ __('packages.reviews_form_submit') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('front.wishlist.partials._toggle-script')
    @endpush
@endsection
