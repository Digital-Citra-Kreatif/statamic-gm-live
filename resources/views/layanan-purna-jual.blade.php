@php
    $bodyClass = collect([
        'background-grey',
        $is_entry ?? false ? 'entry' : null,
        isset($collection) ? 'entry-' . $collection : null,
        isset($collection) ? $collection : null,
        isset($slug) ? 'slug-' . $slug : null,
    ])
        ->filter()
        ->implode(' ');

    $purnaJual = collect($page->sections)->first(
        fn($section) => (string) ($section['identifier'] ?? '') === 'section-purna-jual',
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

        @if ($purnaJual && ($purnaJual['show'] ?? true))
            {{-- Deskripsi purna jual --}}
            @if (!empty($purnaJual['heading']) || !empty($purnaJual['intro']))
                <section id="{{ $purnaJual['anchor'] ?? 'purna-jual' }}">
                    <div class="container flex flex-col items-center my-18 md:my-18 lg:my-30">
                        @if (!empty($purnaJual['heading']))
                            <h2 class="text-left md:text-center lg:text-center mb-2 md:mb-2 lg:mb-3">
                                {{ $purnaJual['heading'] }}
                            </h2>
                        @endif

                        @if (!empty($purnaJual['intro']))
                            <p class="text-left md:text-center lg:text-center lg:w-250">{!! $purnaJual['intro'] !!}</p>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Konten purna jual --}}
            @if (!empty($purnaJual['rows']))
                <section id="items-pruna-jual">
                    <div class="container my-18 md:my-18 lg:my-30">
                        <div id="purna-jual-content" class="reverse-div flex flex-col gap-18 lg:gap-20 w-full">
                            @foreach ($purnaJual['rows'] as $item)
                                <div class="flex flex-col gap-4 md:gap-4 lg:gap-6 lg:flex-row lg:items-stretch">
                                    <img src="{{ $item['image'] ?? '' }}"
                                        alt="{{ $item['image']?->alt ?? ($item['title'] ?? '') }}"
                                        class="w-full object-cover rounded-xl lg:rounded-3xl h-50 md:h-60 lg:h-auto lg:basis-1/2 lg:min-w-0" loading="lazy" decoding="async">
                                    <div
                                        class="flex flex-col justify-center flow bg-white rounded-xl lg:rounded-3xl p-4 md:p-5 lg:p-8 lg:basis-1/2 lg:min-w-0">
                                        @if (!empty($item['icon']))
                                            <img src="{{ $item['icon'] }}" alt="{{ $item['icon']?->alt ?? 'Icon' }}"
                                                class="w-8 h-8 lg:w-10 lg:h-10 mb-8 md:mb-8 lg:mb-10" loading="lazy" decoding="async">
                                        @endif
                                        <h2 class="text-2xl lg:text-3xl normal-case">{{ $item['title'] ?? '' }}</h2>
                                        <p>{{ $item['text'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endif
        {{-- Section tambahan --}}
        <x-layouts.content-rows :rows="$page->content_rows ?? null" />

    </main>

    @if ($hasFooter)
        <x-layouts.footer.footer />
    @endif
</x-layouts.main>
