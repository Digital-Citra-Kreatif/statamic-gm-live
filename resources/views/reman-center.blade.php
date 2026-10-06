@php
    $bodyClass = collect([
        $is_entry ?? false ? 'entry' : null,
        isset($collection) ? 'entry-' . $collection : null,
        isset($collection) ? $collection : null,
        isset($slug) ? 'slug-' . $slug : null,
    ])
        ->filter()
        ->implode(' ');

    $remanSection = collect($page->sections)->first(
        fn($section) => (string) ($section['identifier'] ?? '') === 'opening-reman',
    );

    $imgRemanSection = collect($page->sections)->first(
        fn($section) => (string) ($section['identifier'] ?? '') === 'section-image-reman',
    );

    // Cek component
    $hasHeader = view()->exists('components.layouts.header.header');
    $hasHeroPage = view()->exists('components.layouts.hero.heropage');
    $hasFooter = view()->exists('components.layouts.footer.footer');
@endphp

<x-layouts.main :body-class="$bodyClass">
    @if ($hasHeader)
        <x-layouts.header.header />
    @endif

    <main>
        @if ($hasHeroPage)
            <x-layouts.hero.heropage :title="$page->title" :image="$page->featured_image" />
        @endif

        {{-- Halaman Reman Center --}}
        <section id="reman-center">
            <div class="container">
                <div class="flex flex-col items-center my-18 gap-18 lg:my-30 lg:gap-30">

                    @if ($remanSection && ($remanSection['show'] ?? false))
                        <div id="{{ $remanSection['anchor'] ?? 'reman-center' }}"
                            class="w-full lg:w-240">
                            @if (!empty($remanSection['heading']))
                                <h2 class="mb-6 lg:mb-8">{{ $remanSection['heading'] }}</h2>
                            @endif
                            <div class="bard-content">{!! $remanSection['description'] ?? '' !!}</div>
                        </div>
                    @endif

                    @if ($imgRemanSection && ($imgRemanSection['show'] ?? false))
                        <img id="{{ $imgRemanSection['anchor'] ?? 'image-reman-center' }}"
                            src="{{ $imgRemanSection['section_images'] ?? '' }}" alt=""
                            class="rounded-2xl w-full lg:h-150 object-cover">
                    @endif
                </div>
            </div>
        </section>

    </main>

    @if ($hasFooter)
        <x-layouts.footer.footer />
    @endif
</x-layouts.main>
