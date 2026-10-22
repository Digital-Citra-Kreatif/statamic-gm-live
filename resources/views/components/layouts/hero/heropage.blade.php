@props(['title', 'image', 'height' => 'min-h-[300px] md:min-h-[300px] lg:min-h-[500px]'])

@php
    // URL gambar utama
    if (is_string($image)) {
        $imageUrl = $image ?: null;
        $imageAlt = $title;
    } else {
        $imageUrl = $image?->url() ?? null;
        $imageAlt = $image?->alt ?? $title;
    }

    // Placeholder global set
    if (!$imageUrl) {
        $placeholder = \Statamic\Facades\GlobalSet::findByHandle('hero_page_placeholder')?->inCurrentSite()?->data();
        $placeholderRaw = $placeholder['section_images'] ?? null;

        if ($placeholderRaw) {
            $placeholderAsset = \Statamic\Facades\Asset::find('assets::' . $placeholderRaw);
            $imageUrl = $placeholderAsset?->url() ?? asset('assets/' . $placeholderRaw);
        }
    }
@endphp

<section id="hero-page" class="overflow-hidden">
    <div class="relative {{ $height }}">
        @if ($imageUrl)
            <img src="{{ \App\Support\Img::url($imageUrl, 1920) }}" alt="{{ $imageAlt }}"
                class="absolute inset-0 h-full w-full pointer-events-none object-cover" fetchpriority="high"
                decoding="async" />
        @endif

        <div class="heropage-overlay absolute inset-0"></div>

        <div class="absolute inset-x-0 bottom-6 ld:bottom-10 z-10">
            <div class="container">
                <h1 class="text-left text-white md:text-center lg:text-center">
                    {{ $title }}
                </h1>
            </div>
        </div>
    </div>
</section>
