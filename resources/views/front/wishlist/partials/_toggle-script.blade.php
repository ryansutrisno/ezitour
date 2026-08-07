{{--
    Vanilla JS toggle handler for wishlist heart buttons.

    Include once per page that renders a heart toggle button via:
        @push('scripts')
            @include('front.wishlist.partials._toggle-script')
        @endpush

    Buttons must carry `data-wishlist-toggle` and `data-package-slug`.
    The `.favorited` class swaps the icon to a filled red heart.
--}}
<script>
    (function () {
        var OUTLINE_HEART = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>';
        var FILLED_HEART = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>';

        function renderState(button, isFavorited) {
            if (isFavorited) {
                button.classList.add('favorited');
                button.innerHTML = FILLED_HEART;
                button.setAttribute('title', '{{ __("front.button_remove_wishlist") }}');
                button.setAttribute('aria-label', '{{ __("front.button_remove_wishlist") }}');
            } else {
                button.classList.remove('favorited');
                button.innerHTML = OUTLINE_HEART;
                button.setAttribute('title', '{{ __("front.button_save_wishlist") }}');
                button.setAttribute('aria-label', '{{ __("front.button_save_wishlist") }}');
            }
        }

        async function toggleWishlist(slug, button) {
            var csrfToken = '{{ csrf_token() }}';
            try {
                var res = await fetch('/packages/' + encodeURIComponent(slug) + '/wishlist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (res.status === 401) {
                    button.classList.remove('favorited');
                    button.innerHTML = OUTLINE_HEART;
                    window.location.href = '{{ route("login") }}?pending_wishlist=' + encodeURIComponent(slug) + '&redirect=' + encodeURIComponent(window.location.href);
                    return;
                }
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                var data = await res.json();
                renderState(button, !!data.is_favorited);
            } catch (err) {
                console.error('Wishlist toggle failed', err);
            }
        }

        document.addEventListener('click', async function (event) {
            var btn = event.target.closest('[data-wishlist-toggle]');
            if (!btn) {
                return;
            }
            event.preventDefault();
            var slug = btn.getAttribute('data-package-slug');
            if (!slug) {
                return;
            }
            await toggleWishlist(slug, btn);
        });

        // Expose for any non-click programmatic toggles.
        window.EzitourWishlist = { renderState: renderState };
    })();
</script>
