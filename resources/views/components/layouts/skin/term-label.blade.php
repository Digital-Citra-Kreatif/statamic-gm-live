@props(['term'])

@php
    // Ikon dari taxonomy social_media, dirender solid hijau via CSS mask
    $iconUrl = $term->icon?->url();
@endphp

<span class="inline-flex items-center gap-2">
    @if ($iconUrl)
        <span aria-hidden="true" class="inline-block w-5 h-5 shrink-0 bg-(--color-primary)"
            style="-webkit-mask: url('{{ $iconUrl }}') center / contain no-repeat; mask: url('{{ $iconUrl }}') center / contain no-repeat;"></span>
    @endif
    <span>{{ $term->title }}</span>
</span>
