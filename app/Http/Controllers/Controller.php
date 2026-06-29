<?php

namespace App\Http\Controllers;

use App\Models\Brand;

abstract class Controller
{
    /** Oturumdaki aktif marka slug'ı (varsayılan ilk marka). */
    protected function currentBrandSlug(): string
    {
        return session('brand_slug') ?: optional(Brand::orderBy('id')->first())->slug ?: 'esto';
    }

    /** Aktif marka modeli. */
    protected function currentBrand(): Brand
    {
        return Brand::where('slug', $this->currentBrandSlug())->firstOrFail();
    }
}
