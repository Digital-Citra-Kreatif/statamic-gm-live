@props(['rows' => null])

@php
    $items = collect($rows ?? [])->filter(
        fn($row) => ($row['show'] ?? true) &&
            (filled($row['heading'] ?? null) || filled((string) ($row['text'] ?? '')) || filled((string) ($row['image'] ?? ''))),
    );
@endphp

@if ($items->isNotEmpty())
    <section id="section-tambahan">
        <div class="container my-18 md:my-18 lg:my-30">
            <div class="flex flex-col gap-18 lg:gap-20 w-full">
                @foreach ($items as $row)
                    @php
                        $image = $row['image'] ?? null;
                        $hasImage = filled((string) $image);
                        $heading = (string) ($row['heading'] ?? '');
                        $text = (string) ($row['text'] ?? '');
                        $buttonLabel = (string) ($row['button_label'] ?? '');
                        $buttonLink = (string) ($row['button_link'] ?? '');
                        $hasButton = $buttonLabel !== '' && $buttonLink !== '';
                        $hasText = $heading !== '' || $text !== '' || $hasButton;
                        $position = (string) ($row['image_position'] ?? 'left');
                        // Tanpa teks: gambar ukuran asli
                        $stacked = in_array($position, ['top', 'bottom'], true) || !$hasText;
                        $rowClass = match (true) {
                            $position === 'bottom' => 'flex-col-reverse',
                            $stacked => 'flex-col',
                            $position === 'right' => 'flex-col lg:flex-row-reverse lg:items-stretch',
                            default => 'flex-col lg:flex-row lg:items-stretch',
                        };
                    @endphp

                    <div @if (filled($row['anchor'] ?? null)) id="{{ $row['anchor'] }}" @endif
                        class="flex gap-4 md:gap-4 lg:gap-6 {{ $rowClass }}">
                        {{-- Gambar row --}}
                        @if ($hasImage && $stacked)
                            <img src="{{ \App\Support\Img::url($image, 1200) }}" alt="{{ $image?->alt ?? $heading }}"
                                @if ($image?->width() && $image?->height()) width="{{ $image->width() }}" height="{{ $image->height() }}" @endif
                                class="self-center w-auto max-w-full h-auto rounded-xl lg:rounded-3xl"
                                loading="lazy" decoding="async">
                        @elseif ($hasImage)
                            <img src="{{ \App\Support\Img::url($image, 1200) }}" alt="{{ $image?->alt ?? $heading }}"
                                class="w-full object-cover rounded-xl lg:rounded-3xl h-50 md:h-80 lg:h-auto lg:min-h-100 lg:basis-1/2 lg:min-w-0"
                                loading="lazy" decoding="async">
                        @endif

                        {{-- Teks row --}}
                        @if ($hasText)
                            <div
                                class="flex flex-col justify-center gap-6 lg:gap-8 bg-white rounded-xl lg:rounded-3xl p-4 md:p-5 lg:p-8 lg:min-w-0 {{ $hasImage && !$stacked ? 'lg:basis-1/2' : 'w-full' }}">
                                @if ($heading !== '')
                                    <h2 class="text-2xl lg:text-3xl normal-case">{{ $heading }}</h2>
                                @endif

                                @if ($text !== '')
                                    <div class="content-custom richtext">{!! $text !!}</div>
                                @endif

                                @if ($hasButton)
                                    <a href="{{ $buttonLink }}" class="button gap-4 button--primary w-fit">
                                        <span>{{ $buttonLabel }}</span>
                                        <svg viewBox="0 0 12 12" fill="none" aria-hidden="true" class="h-4 w-4">
                                            <path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
