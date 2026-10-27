<?php

namespace App\Providers;

use App\Bard\CaptionedImageNode;
use App\Bard\TextAlign;
use App\Http\Controllers\CP\Collections\CollectionTreeLabelController;
use App\Http\Controllers\CP\Forms\FormExportController;
use Illuminate\Support\ServiceProvider;
use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Http\Controllers\CP\Collections\CollectionTreeController as StatamicCollectionTreeController;
use Statamic\Http\Controllers\CP\Forms\FormExportController as StatamicFormExportController;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ekspor formulir via stream.
        $this->app->bind(StatamicFormExportController::class, FormExportController::class);
        $this->app->bind(StatamicCollectionTreeController::class, CollectionTreeLabelController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Caption gambar Bard.
        Augmentor::replaceExtension('image', new CaptionedImageNode);

        // Perataan teks Bard.
        Augmentor::replaceExtension('textAlign', new TextAlign([
            'types' => ['heading', 'paragraph'],
            'defaultAlignment' => null,
        ]));

        // CSS control panel.
        Statamic::externalStyle(url('/cp-custom.css').'?v='.@filemtime(public_path('cp-custom.css')));
        Statamic::externalScript(url('/cp-collection-tree.js').'?v='.@filemtime(public_path('cp-collection-tree.js')));
    }
}
