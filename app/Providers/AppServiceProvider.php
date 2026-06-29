<?php

namespace App\Providers;

use App\Models\Brand;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Marka seçici için tüm görünümlere aktif + marka listesi paylaş
        View::composer(['layouts.board', 'studio', 'carousel', 'pano'], function ($view) {
            $brands = Brand::orderBy('id')->get(['id', 'ad', 'slug']);
            $view->with('navBrands', $brands);
            $view->with('navBrandSlug', session('brand_slug') ?: optional($brands->first())->slug);
        });
    }
}
