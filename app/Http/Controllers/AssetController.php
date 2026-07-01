<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /** Görsel kütüphanesi — aktif markanın ürün/modelleri + yüklenmiş fotoğrafları. */
    public function index()
    {
        $brand = $this->currentBrand();

        $items = $brand->catalogItems()
            ->orderBy('kategori')->orderBy('ad')
            ->with(['assets' => fn ($q) => $q->orderByDesc('id')])
            ->get();

        return view('gorseller', [
            'brand' => $brand,
            'items' => $items,
        ]);
    }

    /** Bir ürün/modele fotoğraf yükle. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'catalog_item_id' => 'required|integer|exists:catalog_items,id',
            'shot_type' => 'nullable|string|max:40',
            'foto' => 'required|image|max:15360', // 15MB
        ]);

        $brand = $this->currentBrand();

        $name = 'assets/' . $brand->slug . '/' . $data['catalog_item_id'] . '/' . Str::uuid() . '.' . $request->file('foto')->extension();
        Storage::disk('public')->putFileAs('', $request->file('foto'), $name);

        $asset = Asset::create([
            'brand_id' => $brand->id,
            'catalog_item_id' => $data['catalog_item_id'],
            'dosya' => $name,
            'shot_type' => $data['shot_type'] ?? null,
            'onay_durumu' => 'onayli', // kullanıcı kendi yüklediği için doğrudan onaylı
        ]);

        return response()->json(['ok' => true, 'asset' => $asset, 'url' => asset('storage/'.$name)]);
    }

    /** Fotoğrafı sil. */
    public function destroy(Asset $asset)
    {
        if ($asset->brand_id !== $this->currentBrand()->id) {
            abort(403);
        }

        Storage::disk('public')->delete($asset->dosya);
        $asset->delete();

        return response()->json(['ok' => true]);
    }
}
