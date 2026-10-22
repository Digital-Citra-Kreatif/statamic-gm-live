@php
    $cta = \Statamic\Facades\GlobalSet::findByHandle('career_label_information')?->inCurrentSite()?->toAugmentedArray();

    $contactIcon = $cta['section_images'] ?? null;

    $buildContactUrl = function ($kontak) {
        $kontak = trim((string) $kontak);

        if (str_contains($kontak, '@')) {
            return 'mailto:' . $kontak;
        }

        $number = preg_replace('/[^0-9]/', '', $kontak);
        return 'https://wa.me/' . $number;
    };
@endphp

@if ($cta && ($cta['show'] ?? false))
    <section id="cta-career-single">
        <div class="container my-18 md:my-18 lg:my-30">
            <div class="relative">

                {{-- Background --}}
                @if (!empty($cta['background_call_to_action']))
                    <img src="{{ $cta['background_call_to_action'] }}" alt="" aria-hidden="true"
                        class="inset-0 w-full h-200 md:h-90 lg:h-140 object-cover pointer-events-none rounded-3xl" loading="lazy" decoding="async">
                @endif

                {{-- Konten --}}
                <div
                    class="absolute mt-8 md:mt-0 inset-0 top-0 flex flex-col-reverse gap-10 md:gap-8 lg:gap-20 px-4 md:flex-row lg:flex-row justify-between md:justify-start">

                    {{-- Gambar CTA --}}
                    @if (!empty($cta['image_call_to_action']))
                        <div class="flex items-end lg:justify-center md:w-[40%] lg:w-[45%]">
                            <img src="{{ $cta['image_call_to_action'] }}"
                                alt="{{ $cta['image_call_to_action']?->alt ?? ($cta['heading'] ?? '') }}"
                                class="w-full lg:w-[83%] object-contain" loading="lazy" decoding="async">
                        </div>
                    @endif

                    {{-- Konten teks kontak --}}
                    <div class="flow flex flex-col justify-center md:w-[60%] lg:w-[40%]">
                        @if (!empty($cta['heading']))
                            <h2 class="text-white w-[80%]">{{ $cta['heading'] }}</h2>
                        @endif

                        @if (!empty($cta['short_description']))
                            <p class="text-white w-full">{{ $cta['short_description'] }}</p>
                        @endif

                        {{-- Daftar kontak --}}
                        @if (!empty($cta['call_to_action']))
                            <div class="flex flex-col gap-3 mt-6">
                                @foreach ($cta['call_to_action'] as $contact)
                                    @php
                                        $contactUrl = $buildContactUrl($contact['kontak'] ?? '');
                                        $isWhatsapp = str_starts_with($contactUrl, 'https://wa.me');
                                    @endphp
                                    <a href="{{ $contactUrl }}"
                                        @if ($isWhatsapp) target="_blank" rel="noopener" @endif
                                        class="group bg-(--color-surface) hover:bg-(--color-secondary) flex justify-between items-center rounded-full p-3 pl-6 md:p-3 md:pl-6 lg:p-3 lg:pl-8 transition-colors">

                                        {{-- Kontak --}}
                                        <div class="lg:flex lg:w-[86%] lg:items-center lg:justify-between">
                                            <p class="font-medium group-hover:text-black transition-colors">
                                                {{ $contact['label'] }}
                                            </p>
                                            <span
                                                class="title-display group-hover:text-black -mb-1 transition-colors">{{ $contact['kontak'] }}</span>
                                        </div>

                                        {{-- Icon kontak --}}
                                        @if ($contactIcon)
                                            <div
                                                class="bg-(--color-primary) group-hover:bg-black flex items-center justify-center rounded-full transition-colors w-12 h-12 md:w-12 md:h-12 lg:w-12 lg:h-12">
                                                <img src="{{ $contactIcon }}"
                                                    alt="{{ $contactIcon?->alt ?? $contact['label'] }}" class="w-5 h-5" loading="lazy" decoding="async">
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
