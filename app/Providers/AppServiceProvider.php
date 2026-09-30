<?php

namespace App\Providers;

use App\Bard\CaptionedImageNode;
use App\Bard\TextAlign;
use App\Http\Controllers\CP\Forms\FormExportController;
use Illuminate\Support\ServiceProvider;
use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Http\Controllers\CP\Forms\FormExportController as StatamicFormExportController;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Stream form exports (avoids LiteSpeed ERR_INVALID_RESPONSE on CSV download).
        $this->app->bind(StatamicFormExportController::class, FormExportController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gambar di dalam Bard dirender jadi <figure>+<figcaption> kalau
        // asset-nya punya caption di .meta.
        Augmentor::replaceExtension('image', new CaptionedImageNode);

        // Keep an explicit left alignment when Bard content sits in a centered container.
        Augmentor::replaceExtension('textAlign', new TextAlign([
            'types' => ['heading', 'paragraph'],
            'defaultAlignment' => null,
        ]));

        // CSS tambahan control panel
        // Jangan taruh di /cp > bentrok dengan rute control panel
        Statamic::externalStyle(url('/cp-custom.css'));
    }
}
