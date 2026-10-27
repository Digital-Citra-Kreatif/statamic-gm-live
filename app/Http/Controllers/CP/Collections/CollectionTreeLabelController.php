<?php

namespace App\Http\Controllers\CP\Collections;

use Illuminate\Http\Request;
use Statamic\Contracts\Entries\Collection;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Statamic\Http\Controllers\CP\Collections\CollectionTreeController;

/**
 * Badge label tree
 */
class CollectionTreeLabelController extends CollectionTreeController
{
    public function index(Request $request, Collection $collection)
    {
        $response = parent::index($request, $collection);

        $labeler = match ($collection->handle()) {
            'products' => $this->productLabeler(),
            'dealers' => $this->dealerLabeler(),
            default => null,
        };

        if ($labeler) {
            $response['pages'] = $this->addLabels($response['pages'], $labeler);
        }

        return $response;
    }

    private function productLabeler(): callable
    {
        $categories = Term::whereTaxonomy('product_categories')
            ->mapWithKeys(fn ($term) => [$term->slug() => $term->title()]);

        return function (EntryContract $entry, string $label) use ($categories) {
            $names = collect($entry->get('product_categories') ?? [])
                ->map(fn ($slug) => $categories->get($slug))
                ->filter()
                ->unique()
                ->implode(', ');

            return $names ? $label.' | '.$names : $label;
        };
    }

    private function dealerLabeler(): callable
    {
        $categories = Term::whereTaxonomy('dealer_categories')
            ->mapWithKeys(fn ($term) => [$term->slug() => $term->title()]);

        // Nama dealer + kategori
        return function (EntryContract $entry, string $label) use ($categories) {
            $city = $entry->get('city') ?: $label;
            $name = collect(preg_split('/\R/', (string) $entry->get('address')))
                ->map(fn ($line) => trim($line))
                ->takeWhile(fn ($line) => $line !== '' && mb_strtoupper($line) === $line)
                ->implode(' ');
            $category = collect($entry->get('dealer_categories') ?? [])
                ->map(fn ($slug) => $categories->get($slug))
                ->filter()
                ->unique()
                ->implode(', ');

            return collect([$city, $name, $category])->filter()->implode(' | ');
        };
    }

    private function addLabels(array $pages, callable $labeler): array
    {
        foreach ($pages as &$page) {
            if ($page['entry'] && ($entry = Entry::find($page['entry']))) {
                $titleKey = $page['title'] ? 'title' : 'entry_title';
                $page[$titleKey] = $labeler($entry, (string) $page[$titleKey]);
            }

            $page['children'] = $this->addLabels($page['children'], $labeler);
        }

        return $pages;
    }
}
