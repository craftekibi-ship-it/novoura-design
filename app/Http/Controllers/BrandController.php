<?php

namespace App\Http\Controllers;

use App\Models\Brand;

class BrandController extends Controller
{
    /** Pano — markalar şeridi. */
    public function pano()
    {
        $brands = Brand::orderBy('id')->withCount(['catalogItems', 'posts'])->get();

        return view('pano', [
            'brands' => $brands,
            'current' => $this->currentBrandSlug(),
            'templated' => ['esto', 'serm-barr', 'vail', 'pureline', 'dethleffs-leal', 'novoura'], // şablonu hazır markalar
        ]);
    }

    /** Aktif markayı değiştir. */
    public function switch(string $slug)
    {
        if (Brand::where('slug', $slug)->exists()) {
            session(['brand_slug' => $slug]);
        }

        return redirect('/studio');
    }
}
