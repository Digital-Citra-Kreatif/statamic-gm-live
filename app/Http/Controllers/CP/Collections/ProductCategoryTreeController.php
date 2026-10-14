<?php

namespace App\Http\Controllers\CP\Collections;

use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Statamic\Contracts\Entries\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Statamic\Http\Controllers\CP\Collections\CollectionTreeController;

class ProductCategoryTreeController extends CollectionTreeController
{
    public function index(Request $request, Collection $collection)
    {
        $response = parent::index($request, $collection);

        if ($collection->handle() !== 'products') {
            return $response;
        }

        $categories = Term::whereTaxonomy('product_categories')
            ->mapWithKeys(fn ($term) => [$term->slug() => $term->title()]);

        $response['pages'] = $this->addCategories($response['pages'], $categories);

        return $response;
    }

    private function addCategories(array $pages, SupportCollection $categories): array
    {
        foreach ($pages as &$page) {
            if ($page['entry'] && ($entry = Entry::find($page['entry']))) {
                $names = collect($entry->get('product_categories') ?? [])
                    ->map(fn ($slug) => $categories->get($slug))
                    ->filter()
                    ->unique()
                    ->implode(', ');

                if ($names) {
                    $titleKey = $page['title'] ? 'title' : 'entry_title';
                    $page[$titleKey] .= ' | '.$names;
                }
            }

            $page['children'] = $this->addCategories($page['children'], $categories);
        }

        return $pages;
    }
}
