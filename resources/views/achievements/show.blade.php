<x-layouts.app>
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:py-16">
        <header class="text-center">
            @php
                $yearValue = $page->years ?? [];
                $years =
                    $yearValue instanceof \Illuminate\Support\Collection || is_array($yearValue)
                        ? collect($yearValue)
                        : collect([$yearValue]);
                $years = $years->filter();
            @endphp
            @if ($years->isNotEmpty())
                <p class="text-sm font-semibold uppercase tracking-widest text-emerald-600">
                    @foreach ($years as $year)
                        {{ data_get($year, 'title', is_scalar($year) ? $year : '') }}@unless($loop->last), @endunless
                    @endforeach
                </p>
            @endif

            @if ($page->featured_image)
                <div class="mb-10 mt-6 flex justify-center">
                    @foreach ($page->featured_image as $image)
                        <x-asset-figure
                            :asset="$image"
                            :alt="$page->title"
                            class="max-h-80 w-auto object-contain"
                        />
                    @endforeach
                </div>
            @endif

            <h1 class="mt-3 text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl dark:text-zinc-100">
                {{ $page->title }}
            </h1>

            @if ($page->description)
                <div class="bard-content prose prose-zinc mx-auto mt-6 max-w-2xl dark:prose-invert">
                    {!! $page->description !!}
                </div>
            @endif
        </header>
    </article>
</x-layouts.app>
