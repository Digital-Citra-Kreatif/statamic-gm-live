@props([
    'asset',
    'alt' => null,
    'figure' => true,
])

@php
    $altText = $alt ?? $asset->alt ?? $asset->title ?? '';
    $caption = $asset->caption ?? null;
@endphp

@if ($caption && $figure)
    <figure>
        <img src="{{ $asset->url }}" alt="{{ $altText }}" {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
        <figcaption class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
            {{ $caption }}
        </figcaption>
    </figure>
@elseif ($caption)
    <div>
        <img src="{{ $asset->url }}" alt="{{ $altText }}" {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $caption }}</p>
    </div>
@else
    <img src="{{ $asset->url }}" alt="{{ $altText }}" {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
@endif
