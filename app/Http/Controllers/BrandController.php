<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

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

    /** Aktif markayı değiştir. Pano'dan "Aç" ile gelindiyse Stüdyo'ya git, nav dropdown'dan gelindiyse bulunulan sayfada kal. */
    public function switch(Request $request, string $slug)
    {
        if (Brand::where('slug', $slug)->exists()) {
            session(['brand_slug' => $slug]);
        }

        if ($request->query('open') === 'studio') {
            return redirect('/studio');
        }

        return redirect()->back(fallback: '/pano');
    }
}
