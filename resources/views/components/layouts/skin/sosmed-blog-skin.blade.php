@props(['entry', 'blog' => null, 'currentCategory' => null])

@php
    $blog =
        $blog ??
        \Statamic\Facades\GlobalSet::findByHandle('blog_label_information')
            ?->in(\Statamic\Facades\Site::current()->handle())
            ?->toAugmentedArray();

    $social = collect($entry->social_media ?? []);
    $cats = collect($entry->categories ?? []);

    if ($social->isNotEmpty()) {
        $displayTerm = $social->first();
    } elseif ($currentCategory) {
        $displayTerm = $cats->firstWhere(fn($c) => (string) $c->slug() === (string) $currentCategory) ?? $cats->first();
    } else {
        $displayTerm = $cats->first();
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

<article class="group overflow-hidden rounded-3xl bg-(--color-surface) flex flex-col">
    <a href="{{ $cardUrl }}"
        @if ($cardTarget) target="{{ $cardTarget }}" rel="{{ $cardRel }}" @endif
        class="flex flex-col">

        {{-- Featured Image --}}
        <div class="overflow-hidden">
            <img src="{{ \App\Support\Img::url($entry->featured_image?->url() ?? ($blog['image_placeholders'] ?? ''), 800) }}"
                alt="{{ $entry->featured_image?->alt ?? $entry->title }}"
                class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" decoding="async" />
        </div>

        <div class="p-5 flex flex-col gap-10">

            {{-- Kategori, tanggal --}}
            <div class="richtext flex flex-col gap-4">
                <div
                    class="flex items-center gap-5 uppercase text-(--color-primary) font-medium text-sm lg:text-base justify-between">
                    @if ($displayTerm)
                        <x-layouts.skin.term-label :term="$displayTerm" />
                    @endif

                    @if ($entry->date)
                        <span>{{ $entry->date->format('d.m.Y') }}</span>
                    @endif
                </div>

                {{-- Heading --}}
                <div class="custom-heading-blog">
                    <p class="text-(--color-heading) title-display text-xl tracking-tight lg:text-2xl">
                        {{ $entry->title }}</p>
                </div>

                @if ($entry->excerpt)
                    <p>{{ \Illuminate\Support\Str::words($entry->excerpt, 18, '...') }}</p>
                @endif
            </div>

            {{-- Button --}}
            <a href="{{ $cardUrl }}"
                @if ($cardTarget) target="{{ $cardTarget }}" rel="{{ $cardRel }}" @endif
                class="rounded-full py-2 pr-2 pl-6 flex items-center justify-between bg-white group-hover:bg-(--color-primary)">
                <p class="uppercase title-display text-(--color-primary) tracking-widest group-hover:text-white -mb-1">
                    {{ $blog['label_button'] ?? 'Selengkapnya' }}
                </p>
                <span
                    class="w-9 h-9 rounded-full bg-(--color-primary) group-hover:bg-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white group-hover:text-(--color-primary)" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </a>

        </div>

    </a>
</article>
