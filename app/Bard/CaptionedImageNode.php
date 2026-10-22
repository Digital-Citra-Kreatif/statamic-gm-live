<?php

namespace App\Bard;

use Statamic\Facades\Asset;
use Statamic\Fieldtypes\Bard\ImageNode;
use Statamic\Support\Str;
use Tiptap\Utils\HTML;

/** Gambar caption Bard */
class CaptionedImageNode extends ImageNode
{
    // Lazyload gambar Bard
    public function addOptions()
    {
        return [
            'HTMLAttributes' => ['loading' => 'lazy', 'decoding' => 'async'],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = [])
    {
        $caption = $this->captionFor($node->attrs->src ?? null);

        if ($caption === null) {
            return parent::renderHTML($node, $HTMLAttributes);
        }

        // Render figure HTML
        $attributes = HTML::renderAttributes(
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes)
        );

        return ['content' => sprintf(
            '<figure class="bard-figure"><img%s><figcaption class="bard-figcaption">%s</figcaption></figure>',
            $attributes,
            e($caption)
        )];
    }

    protected function captionFor($src): ?string
    {
        if (! $src || ! Str::startsWith($src, 'asset::')) {
            return null;
        }

        $asset = Asset::find(Str::after($src, 'asset::'));

        if (! $asset) {
            return null;
        }

        $caption = trim((string) ($asset->get('caption') ?? ''));

        return $caption === '' ? null : $caption;
    }
}
