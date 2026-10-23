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

    $remanRows = collect($page->sections)->first(
        fn($section) => (string) ($section['type'] ?? '') === 'alternating_rows',
    );

    $remanFeatures = collect($page->sections)->first(
        fn($section) => (string) ($section['type'] ?? '') === 'feature_grid',
    );

    // Jumlah kolom grid
    $columnClassMap = [
        '1' => 'lg:grid-cols-1',
        '2' => 'md:grid-cols-2 lg:grid-cols-2',
        '3' => 'md:grid-cols-2 lg:grid-cols-3',
        '4' => 'md:grid-cols-2 lg:grid-cols-4',
    ];

    $featureColumns = $columnClassMap[(string) ($remanFeatures['columns'] ?? '3')] ?? 'md:grid-cols-2 lg:grid-cols-3';

    $otherDescription = collect($page->sections)->first(
        fn($section) => (string) ($section['type'] ?? '') === 'other_description',
    );

    // Baris "- " jadi list
    $rowTextLines = fn($text) => collect(preg_split('/\R/', trim((string) $text)))
        ->map(fn($line) => trim($line))
        ->filter();

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
                <div class="flex flex-col my-18 gap-18 lg:my-30 lg:gap-30">

                    {{-- Judul & alternating rows --}}
                    <div class="flex flex-col gap-10 lg:gap-14 w-full">
                        @if ($remanSection && ($remanSection['show'] ?? false))
                            <div id="{{ $remanSection['anchor'] ?? 'reman-center' }}"
                                class="w-full text-left lg:text-center">
                                @if (!empty($remanSection['heading']))
                                    <h2 @class(['mb-6 lg:mb-8' => filled((string) ($remanSection['description'] ?? ''))])>
                                        {{ $remanSection['heading'] }}</h2>
                                @endif
                                @if (filled((string) ($remanSection['description'] ?? '')))
                                    <div class="content-custom richtext">{!! $remanSection['description'] !!}</div>
                                @endif
                            </div>
                        @endif

                        {{-- Alternating rows --}}
                        @if ($remanRows && ($remanRows['show'] ?? true) && !empty($remanRows['rows']))
                            <div id="{{ $remanRows['anchor'] ?? 'layanan-reman' }}" class="w-full">
                                @if (!empty($remanRows['heading']) || !empty($remanRows['intro']))
                                    <div class="flex flex-col mb-18 lg:mb-20">
                                        @if (!empty($remanRows['heading']))
                                            <h2 class="mb-2 md:mb-2 lg:mb-3">
                                                {{ $remanRows['heading'] }}</h2>
                                        @endif
                                        @if (!empty($remanRows['intro']))
                                            <p>{{ $remanRows['intro'] }}</p>
                                        @endif
                                    </div>
                                @endif

                                <div class="reverse-div flex flex-col gap-18 lg:gap-20 w-full">
                                    @foreach ($remanRows['rows'] as $item)
                                        @php
                                            $lines = $rowTextLines($item['text'] ?? '');
                                            $isList = $lines->isNotEmpty() && $lines->every(fn($line) => str_starts_with($line, '- '));
                                            $hasImage = filled((string) ($item['image'] ?? ''));
                                        @endphp
                                        <div class="flex flex-col gap-4 md:gap-4 lg:gap-6 lg:flex-row lg:items-stretch">
                                            @if ($hasImage)
                                                <img src="{{ \App\Support\Img::url($item['image'], 800) }}"
                                                    alt="{{ $item['image']?->alt ?? ($item['title'] ?? '') }}"
                                                    class="w-full object-cover rounded-xl lg:rounded-3xl h-50 md:h-60 lg:h-auto lg:min-h-100 lg:basis-1/2 lg:min-w-0"
                                                    loading="lazy" decoding="async">
                                            @endif
                                            <div
                                                class="flex flex-col justify-center gap-6 lg:gap-8 bg-(--color-surface) rounded-xl lg:rounded-3xl p-4 md:p-5 lg:p-8 lg:min-w-0 {{ $hasImage ? 'lg:basis-1/2' : 'w-full' }}">
                                                @if (!empty($item['icon']))
                                                    <img src="{{ \App\Support\Img::url($item['icon'], 200) }}" alt="{{ $item['icon']?->alt ?? 'Icon' }}"
                                                        class="w-8 h-8 lg:w-10 lg:h-10" loading="lazy" decoding="async">
                                                @endif
                                                <h3 class="text-2xl lg:text-3xl normal-case">{{ $item['title'] ?? '' }}</h3>
                                                <div class="content-custom richtext">
                                                    @if ($isList)
                                                        <ul class="ms-0! ps-5!">
                                                            @foreach ($lines as $line)
                                                                <li>{{ substr($line, 2) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        @foreach ($lines as $line)
                                                            <p>{{ $line }}</p>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Skema layanan (feature grid) --}}
                    @if ($remanFeatures && ($remanFeatures['show'] ?? false) && !empty($remanFeatures['features']))
                        <div id="{{ $remanFeatures['anchor'] ?? 'skema-layanan-reman' }}"
                            class="flex flex-col gap-8 md:gap-8 lg:gap-10 w-full">
                            @if (!empty($remanFeatures['heading']) || !empty($remanFeatures['subheading']))
                                <div class="flow text-left md:text-left lg:text-center">
                                    @if (!empty($remanFeatures['heading']))
                                        <h2>{{ $remanFeatures['heading'] }}</h2>
                                    @endif
                                    @if (!empty($remanFeatures['subheading']))
                                        <p>{{ $remanFeatures['subheading'] }}</p>
                                    @endif
                                </div>
                            @endif

                            <div data-equal-height class="grid gap-5 {{ $featureColumns }}">
                                @foreach ($remanFeatures['features'] as $item)
                                    <div
                                        class="flex flex-col gap-8 p-4 bg-(--color-surface) rounded-xl lg:rounded-3xl md:p-4 md:gap-8 lg:p-6 lg:gap-12">
                                        @if (!empty($item['icon']))
                                            <img src="{{ \App\Support\Img::url($item['icon'], 200) }}"
                                                alt="{{ $item['icon']?->alt ?? 'Icon' }}" class="w-8 h-8 lg:w-10 lg:h-10"
                                                loading="lazy" decoding="async">
                                        @endif
                                        <div class="flex flex-col gap-2 lg:gap-4">
                                            <p
                                                class="w-full text-(--color-heading) title-display text-xl tracking-tight lg:text-2xl">
                                                {{ $item['title'] ?? '' }}</p>
                                            @foreach ($rowTextLines($item['text'] ?? '') as $line)
                                                <p>{{ $line }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Other Description --}}
                    @if ($otherDescription && ($otherDescription['show'] ?? false) && filled((string) ($otherDescription['description'] ?? '')))
                        <div id="{{ $otherDescription['anchor'] ?? 'other-description-reman' }}" class="w-full">
                            <div class="content-custom richtext">{!! $otherDescription['description'] !!}</div>
                        </div>
                    @endif

                    @if ($imgRemanSection && ($imgRemanSection['show'] ?? false))
                        <img id="{{ $imgRemanSection['anchor'] ?? 'image-reman-center' }}"
                            src="{{ \App\Support\Img::url($imgRemanSection['section_images'] ?? '', 1200) }}" alt=""
                            class="rounded-2xl w-full lg:h-150 object-cover" loading="lazy" decoding="async">
                    @endif
                </div>
            </div>
        </section>

        {{-- Section tambahan --}}
        <x-layouts.content-rows :rows="$page->content_rows ?? null" />

    </main>

    @if ($hasFooter)
        <x-layouts.footer.footer />
    @endif
</x-layouts.main>
