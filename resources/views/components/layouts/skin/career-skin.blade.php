@props(['entry', 'career' => null])

@php
    $career =
        $career ??
        \Statamic\Facades\GlobalSet::findByHandle('career_label_information')
            ?->in(\Statamic\Facades\Site::current()->handle())
            ?->toAugmentedArray();

    $resolveLocations = function ($field) {
        if ($field instanceof \Statamic\Fields\Value) {
            $field = $field->value();
        }
        if ($field instanceof \Statamic\Contracts\Query\Builder) {
            $field = $field->get();
        }

        if ($field instanceof \Illuminate\Support\Collection || is_array($field)) {
            return collect($field)->filter(fn ($location) => is_object($location) && filled($location->title ?? null));
        }

        if (is_object($field) && filled($field->title ?? null)) {
            return collect([$field]);
        }

        return collect();
    };

    $entryLocations = $resolveLocations($entry->locations);
    $entryLocationTitle = $entryLocations->first()?->title;
@endphp

<a href="{{ $entry->url() }}"
    class="group hover:bg-[#E8F1E8] flex flex-col gap-6 md:gap-3 lg:gap-8 rounded-2xl lg:rounded-3xl bg-white p-2 md:p-2 lg:p-2 justify-between">

    <div class="px-3 pt-3 md:px-2 lg:px-4 md:pt-2 lg:pt-4 flex flex-col gap-6 md:gap-3 lg:gap-8">
        <header class="flex flex-col gap-3">

            {{-- Employment & Location --}}
            <div class="flex flex-wrap gap-4 md:gap-6">
                @if ($entry->employment_status && $entry->employment_status->label())
                    <p
                        class="flex items-center gap-2 uppercase text-(--color-primary) font-medium group-hover:text-black">
                        @if (!empty($career['icon_employment_status']))
                            <img src="{{ $career['icon_employment_status'] }}"
                                alt="{{ $career['icon_employment_status']?->alt }}"
                                class="w-4 h-4 shrink-0 -mt-1 group-hover:brightness-0" loading="lazy" decoding="async" />
                        @endif
                        {{ $entry->employment_status->label() }}
                    </p>
                @endif

                @if (filled($entryLocationTitle))
                    <p
                        class="flex items-center gap-2 uppercase text-(--color-primary) font-medium group-hover:text-black">
                        @if (!empty($career['icon_location']))
                            <img src="{{ $career['icon_location'] }}" alt="{{ $career['icon_location']?->alt }}"
                                class="w-4 h-4 shrink-0 -mt-1 group-hover:brightness-0" loading="lazy" decoding="async" />
                        @endif
                        {{ $entryLocationTitle }}
                    </p>
                @endif
            </div>

            {{-- Heading --}}
            <p class="text-(--color-heading) tracking-tight title-display text-2xl">{{ $entry->title }}</p>
        </header>

        <div class="flex flex-col gap-4">

            {{-- Excerpt --}}
            @if ($entry->excerpt)
                <p>{{ $entry->excerpt }}</p>
            @endif

            {{-- Tags --}}
            @if ($entry->tags)
                <div class="flex flex-wrap gap-2">
                    @foreach ($entry->tags as $tag)
                        <p class="rounded-full bg-(--color-surface) px-3.5 py-2 text-xs lg:text-sm">
                            {{ $tag->title }}
                        </p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Button Selengkapnya --}}
    <div
        class="mt-4 bg-(--color-surface) rounded-full flex justify-between items-center py-2 pl-6 pr-2 group-hover:bg-(--color-primary)">
        <p class="uppercase title-display text-(--color-primary) tracking-widest group-hover:text-white">
            {{ $career['button_label'] ?? 'Selengkapnya' }}
        </p>
        <p
            class="flex h-10 w-10 md:h-8 md:w-8 lg:h-10 lg:w-10 shrink-0 items-center justify-center rounded-full bg-(--color-primary) group-hover:bg-white">
            <svg class="h-4 w-4 text-white group-hover:text-(--color-primary)" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </p>
    </div>

</a>
