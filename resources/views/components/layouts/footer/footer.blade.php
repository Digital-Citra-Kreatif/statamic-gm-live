@props([
    'compact' => false,
])

@php
    $footer = \Statamic\Facades\GlobalSet::findByHandle('primary_footer')?->inCurrentSite();

    // URL Button
    $footerUrlRaw = $footer?->augmentedValue('url_button')->value();
    $footerUrl = is_object($footerUrlRaw) ? $footerUrlRaw->url() ?? (string) $footerUrlRaw : $footerUrlRaw;
    $footerUrl = $footerUrl ?: null;

    $globals = \Statamic\Facades\GlobalSet::findByHandle('settings')?->inCurrentSite()?->data();

    $company_name = $globals['site_title'] ?? 'PT Gaya Makmur Mobil';

    $socials = collect($globals['social_media'] ?? [])
        ->map(function ($item, $key) {
            $url = $item['url'] ?? null;
            if (!$url) {
                return null;
            }

            $iconRaw = $item['image'] ?? null;

            return [
                'name' => ucfirst($key),
                'link' => $url,
                'icon' => $iconRaw ? asset('assets/' . $iconRaw) : null,
            ];
        })
        ->filter()
        ->values()
        ->all();
@endphp

@if ($footer?->get('show'))
    <footer id="footer" class="{{ $compact ? 'lg:mt-30' : 'lg:mt-50' }}">
        <div class="relative overflow-hidden md:overflow-visible lg:overflow-visible">

            {{-- Background Footer --}}
            <div id="footer-background" class="overlay-footer">
                <img src="{{ $footer->augmentedValue('backgound_image')->value()?->url() }}"
                    alt="{{ $footer->augmentedValue('backgound_image')->value()?->alt ?? 'Footer Background' }}"
                    class="block w-full h-200 md:h-110 lg:h-120 object-cover pointer-events-none rounded-t-3xl lg:rounded-t-[60px]" loading="lazy" decoding="async">
            </div>

            {{-- Content Footer --}}
            <div id="content-footer" class="absolute inset-0 z-10 bottom-0">
                <div class="container flex flex-col gap-8 justify-center md:flex-row lg:flex-row">

                    <div class="flex flex-col-reverse md:flex-row lg:flex-row gap-6 md:gap-8 lg:gap-10">

                        {{-- Image Footer --}}
                        <div id="image-footer" class="flex items-end justify-center mb-0 md:w-[60%] lg:w-[50%]">
                            <img src="{{ $footer->augmentedValue('image')->value()?->url() }}"
                                alt="{{ $footer->augmentedValue('image')->value()?->alt ?? $footer->get('heading') }}"
                                class="w-auto max-w-full h-auto max-h-104 md:max-h-110 lg:max-h-148 object-contain object-bottom md:-mt-16 lg:-mt-28" loading="lazy" decoding="async">
                        </div>

                        {{-- CTA Footer --}}
                        <div id="cta-footer"
                            class="flex flex-col justify-between md:w-[70%] lg:w-[50%] mt-14 md:mt-0 lg:mt-0">
                            <div class="flow md:mt-8 lg:mt-18">
                                <h2 class="text-white lg:w-110">{{ $footer->get('heading') }}</h2>
                                <p class="text-white lg:w-120">{{ $footer->get('short_description') }}</p>

                                {{-- Media Sosial --}}
                                <div
                                    class="flex flex-col-reverse gap-6 items-start mt-4 lg:mt-8 lg:flex-row lg:items-center">
                                    @if (count($socials) > 0)
                                        <div
                                            class="flex justify-between w-full border-t border-white/20 py-4 mt-2 md:border-white/0 md:py-0 md:mt-0 lg:border-white/0 lg:py-0 lg:mt-0 lg:w-min">
                                            <p class="uppercase text-white border-white md:hidden lg:hidden">
                                                {{ $footer->get('label_social_media') }}</p>
                                            <div class="flex gap-4">
                                                @foreach ($socials as $social)
                                                    <a href="{{ $social['link'] }}" target="_blank"
                                                        rel="noopener noreferrer" title="{{ $social['name'] }}">
                                                        <span class="social-icon block w-5 h-5 social-icon-white"
                                                            style="--icon-url: url('{{ $social['icon'] }}');"></span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Button Konsultasi --}}
                                    @if ($footerUrl)
                                        <a href="{{ $footerUrl }}" class="button gap-4 button--white">
                                            <span>{{ $footer->get('label_button') ?: 'Konsultasi Sekarang' }}</span>
                                            <svg viewBox="0 0 12 12" fill="none" aria-hidden="true" class="h-4 w-4">
                                                <path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Copyright Footer --}}
                            <div id="copyrigth-footer" class="hidden md:block pb-4 lg:pb-4">
                                <p class="text-white">© {{ date('Y') }} {{ $company_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright Footer (mobile) --}}
        <div id="copyrigth-footer-mobile" class="md:hidden relative z-10 bg-(--color-primary) p-4 -mt-2">
            <p class="text-white text-center">© {{ date('Y') }} {{ $company_name }}</p>
        </div>
    </footer>
@endif
