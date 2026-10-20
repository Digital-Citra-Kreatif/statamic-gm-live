@props(['entry', 'blog' => null, 'currentCategory' => null])

@php
    $blog =
        $blog ??
        \Statamic\Facades\GlobalSet::findByHandle('blog_label_information')
            ?->in(\Statamic\Facades\Site::current()->handle())
            ?->toAugmentedArray();

    $cats = collect($entry->categories ?? []);

    $social = collect($entry->social_media ?? []);

    // Kategori sosial-media
    if ((string) $currentCategory === 'sosial-media') {
        if ($social->isNotEmpty()) {
            $displayTerms = $social->take(2);
        } else {
            $displayTerms = $cats->filter(fn($c) => (string) $c->slug() === 'sosial-media')->take(1);
        }
    } elseif ($currentCategory) {
        $active = $cats->filter(fn($c) => (string) $c->slug() === (string) $currentCategory);
        $rest = $cats->reject(fn($c) => (string) $c->slug() === (string) $currentCategory);
        $displayTerms = $active->concat($rest)->take(2);
    } else {
        $displayTerms = $cats->take(2);
    }

    // Post kategori sosial-media
    $isSocial = $cats->contains(fn($c) => (string) $c->slug() === 'sosial-media');

    $rawLink = $isSocial ? $entry->value('social_media_links') : null;
    if (is_string($rawLink) && str_starts_with($rawLink, 'entry::')) {
        $socialLink = \Statamic\Facades\Entry::find(str_replace('entry::', '', $rawLink))?->url() ?? '';
    } else {
        $socialLink = is_string($rawLink) ? $rawLink : '';
    }

    $cardUrl = $socialLink !== '' ? $socialLink : $entry->url();

    $cardTarget = $socialLink !== '' ? '_blank' : null;

    $cardRel = $socialLink !== '' ? 'noopener noreferrer' : null;
@endphp

<article class="skin-blog-cust group overflow-hidden rounded-3xl bg-white flex flex-col">
    <a href="{{ $cardUrl }}"
        @if ($cardTarget) target="{{ $cardTarget }}" rel="{{ $cardRel }}" @endif
        class="flex-cust flex flex-col flex-1">

        {{-- Featured Image --}}
        <div class="img-high overflow-hidden">
            <img src="{{ $entry->featured_image?->url() ?? ($blog['image_placeholders'] ?? '') }}"
                alt="{{ $entry->featured_image?->alt ?? $entry->title }}"
                class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-500" />
        </div>

        <div class="p-5 flex flex-col gap-10 justify-between flex-1">

            {{-- Heading --}}
            <div class="flex flex-col gap-2 md:gap-2 lg:gap-3">
                <p
                    class="font-(family-name:--font-display) font-semibold text-(--color-heading) text-xl lg:text-2xl tracking-tight">
                    {{ $entry->title }}</p>

                @if ($entry->excerpt)
                    <p>{{ \Illuminate\Support\Str::words($entry->excerpt, 18, '...') }}</p>
                @endif
            </div>

            {{-- Kategori - Tanggal --}}
            <div class="flex items-end justify-between">
                <div class="flex items-center gap-5 uppercase text-(--color-primary) font-medium text-sm lg:text-base">
                    @if ($displayTerms->isNotEmpty())
                        <span>
                            @foreach ($displayTerms as $term)
                                <x-layouts.skin.term-label :term="$term" />
                                @unless ($loop->last)
                                    ,
                                @endunless
                            @endforeach
                        </span>
                        @if ($entry->date)
                            <span>•</span>
                        @endif
                    @endif
                    @if ($entry->date)
                        <span>{{ $entry->date->format('d.m.Y') }}</span>
                    @endif
                </div>

                {{-- Chevron --}}
                <span
                    class="shrink-0 text-white group-hover:text-black bg-(--color-primary) group-hover:bg-(--color-secondary) w-10 h-10 rounded-full flex justify-center items-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </div>

        </div>

    </a>
</article>
