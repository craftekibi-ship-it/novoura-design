<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /** Stüdyodan post kaydet (yeni veya güncelle). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer',
            'catalog_item_id' => 'nullable|integer', // carousel birden çok yemek içerebilir
            'gorsel_yazilari' => 'required|array',
            'caption' => 'nullable|string',
            'durum' => 'required|string',
            'image' => 'nullable|string', // data:image/png;base64,...
        ]);

        $brand = $this->currentBrand();

        $post = !empty($data['id']) ? Post::findOrFail($data['id']) : new Post();
        $post->brand_id = $brand->id;
        $post->catalog_item_id = $data['catalog_item_id'] ?? null;
        $post->gorsel_yazilari_json = $data['gorsel_yazilari'];
        $post->caption = $data['caption'] ?? null;
        $post->durum = $data['durum'];

        // Önizleme PNG'sini kaydet (kuyruk/takvim için kapak)
        if (!empty($data['image']) && str_starts_with($data['image'], 'data:image')) {
            $binary = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $data['image']));
            if ($binary !== false) {
                $name = 'posts/' . Str::uuid() . '.png';
                Storage::disk('public')->put($name, $binary);
                if ($post->export_yolu) {
                    Storage::disk('public')->delete($post->export_yolu);
                }
                $post->export_yolu = $name;
            }
        }

        $post->save();

        return response()->json([
            'id' => $post->id,
            'thumb' => $post->export_yolu ? Storage::url($post->export_yolu) : null,
        ]);
    }

    /** Durum güncelle (Onayla / geri al vb.) */
    public function updateStatus(Post $post, Request $request)
    {
        $post->durum = $request->validate(['durum' => 'required|string'])['durum'];
        $post->save();
        return response()->json(['ok' => true, 'durum' => $post->durum]);
    }

    /** Planlanan tarih ata. */
    public function plan(Post $post, Request $request)
    {
        $v = $request->validate(['tarih' => 'nullable|date']);
        $post->planlanan_tarih = $v['tarih'] ?: null;
        if ($post->planlanan_tarih && $post->durum === 'onayli') {
            $post->durum = 'planlandi';
        }
        $post->save();
        return response()->json(['ok' => true]);
    }

    public function destroy(Post $post)
    {
        if ($post->export_yolu) {
            Storage::disk('public')->delete($post->export_yolu);
        }
        $post->delete();
        return response()->json(['ok' => true]);
    }

    /** Onay kuyruğu ekranı. */
    public function queue()
    {
        $posts = Post::with('catalogItem')
            ->whereHas('brand', fn ($q) => $q->where('slug', $this->currentBrandSlug()))
            ->orderByRaw("CASE durum WHEN 'onay_bekliyor' THEN 0 WHEN 'taslak' THEN 1 WHEN 'onayli' THEN 2 WHEN 'planlandi' THEN 3 ELSE 4 END")
            ->latest()
            ->get();

        return view('kuyruk', ['posts' => $posts]);
    }

    /** Takvim ekranı. */
    public function calendar(Request $request)
    {
        $ay = $request->query('ay', now()->format('Y-m')); // 2026-06
        [$y, $m] = array_map('intval', explode('-', $ay));

        $planli = Post::with('catalogItem')
            ->whereHas('brand', fn ($q) => $q->where('slug', $this->currentBrandSlug()))
            ->whereNotNull('planlanan_tarih')
            ->whereYear('planlanan_tarih', $y)
            ->whereMonth('planlanan_tarih', $m)
            ->get()
            ->groupBy(fn ($p) => $p->planlanan_tarih->format('Y-m-d'));

        $planlanmamis = Post::with('catalogItem')
            ->whereHas('brand', fn ($q) => $q->where('slug', $this->currentBrandSlug()))
            ->whereNull('planlanan_tarih')
            ->whereIn('durum', ['onayli', 'onay_bekliyor', 'taslak'])
            ->latest()->get();

        return view('takvim', compact('ay', 'y', 'm', 'planli', 'planlanmamis'));
    }
}
