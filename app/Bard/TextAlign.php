<?php

namespace App\Bard;

use Tiptap\Extensions\TextAlign as BaseTextAlign;

class TextAlign extends BaseTextAlign
{
    public function addGlobalAttributes()
    {
        $attributes = parent::addGlobalAttributes();

        $attributes[0]['attributes']['textAlign']['renderHTML'] = function ($nodeAttributes) {
            $alignment = $nodeAttributes->textAlign ?? null;

            return in_array($alignment, ['left', 'center', 'right', 'justify'], true)
                ? ['style' => "text-align: {$alignment}"]
                : null;
        };

        return $attributes;
    }
}
