@if (filled($page->sections))
    @include('partials.page-sections')
@elseif (filled($page->content))
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:py-16">
        <div class="bard-content prose prose-zinc dark:prose-invert max-w-none">
            {!! Statamic::modify($page->content)->widont() !!}
        </div>
    </article>
@endif
