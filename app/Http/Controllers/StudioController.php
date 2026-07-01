<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CatalogItem;
use App\Models\Post;
use App\Services\AnthropicService;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    /**
     * Post stüdyosu — Esto şablonu + canvas editör.
     */
    public function show(?int $itemId = null)
    {
        $brand = $this->currentBrand();
        $item = $itemId ? CatalogItem::find($itemId) : null;

        return $this->render($brand, $item, null);
    }

    /**
     * Kayıtlı bir postu stüdyoda aç (metni yüklü gelir, fotoğraf eklenir).
     */
    public function openPost(Post $post)
    {
        return $this->render($post->brand, $post->catalogItem, $post);
    }

    private function render(Brand $brand, ?CatalogItem $item, ?Post $post)
    {
        $items = $brand->catalogItems()
            ->orderBy('kategori')->orderBy('ad')
            ->get(['id', 'ad', 'kategori']);

        return view('studio', [
            'brand' => $brand,
            'items' => $items,
            'item' => $item,
            'post' => $post,
            'template' => $this->brandTemplate($brand),
        ]);
    }

    /**
     * AI içerik üretimi (proxy). Anahtar tarayıcıya düşmez.
     */
    public function generate(int $itemId, AnthropicService $ai)
    {
        $item = CatalogItem::findOrFail($itemId);
        return response()->json($ai->generatePostContent($item));
    }

    /**
     * Toplu üretim ekranı — bir kategorinin tüm yemekleri için içerik.
     */
    public function batch()
    {
        $brand = $this->currentBrand();
        $items = $brand->catalogItems()
            ->orderBy('kategori')->orderBy('ad')
            ->get(['id', 'ad', 'kategori']);

        return view('toplu', [
            'brand' => $brand,
            'items' => $items,
            'kategoriler' => $items->pluck('kategori')->filter()->unique()->values(),
        ]);
    }

    /**
     * Carousel editörü — çok slaytlı post.
     */
    public function carousel()
    {
        $brand = $this->currentBrand();
        $items = $brand->catalogItems()
            ->orderBy('kategori')->orderBy('ad')
            ->get(['id', 'ad', 'kategori']);

        return view('carousel', [
            'brand' => $brand,
            'items' => $items,
            'template' => $this->brandTemplate($brand),
        ]);
    }

    /**
     * Marka şablon tanımı. Her marka kendi tasarımıyla buraya eklenir
     * (çerçeve = JS render anahtarı). Şablonu olmayan marka null döner.
     */
    private function brandTemplate(Brand $brand): ?array
    {
        $templates = [
            'esto' => [
                'id' => 'esto-post-01',
                'frame' => 'esto',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#E8943A',
                'phone' => '+90 536 929 70 69',
                'website' => ['www.estorestaurant.com', 'ru.estorestaurant.com'],
                'fonts' => ['script' => 'Caveat', 'body' => 'Poppins'],
                'text_overlay' => true, // gerçek Instagram'da her postta tutarlı kullanılıyor
            ],
            'serm-barr' => [
                'id' => 'serm-barr-post-01',
                'frame' => 'serm-barr',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#C9A24B',           // altın vurgu
                'phone' => '+90 534 695 77 70',
                'website' => ['www.sermbarr.com'],
                'fonts' => ['script' => 'Poppins', 'body' => 'Poppins'], // sade, el yazısı yok
                'text_overlay' => false, // gerçek ürün fotoları tamamen temiz, sadece filigran
            ],
            'vail' => [
                'id' => 'vail-post-01',
                'frame' => 'vail',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#9DC3CE',           // açık teal vurgu (foto üstünde okunur)
                'phone' => '+90 534 695 77 70',
                'website' => ['www.vailcaravan.com'],
                'fonts' => ['script' => 'Poppins', 'body' => 'Poppins'],
                'text_overlay' => false, // gerçek ürün fotoları temiz, mesaj caption'da
            ],
            'pureline' => [
                'id' => 'pureline-post-01',
                'frame' => 'pureline',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#29ABE2',           // açık mavi vurgu (gerçek site paleti)
                'phone' => '',
                'website' => ['www.purelinecleaning.com'],
                'fonts' => ['script' => 'Poppins', 'body' => 'Poppins'],
                'text_overlay' => false, // sade/kurumsal temizlik markası, minimal görsel
            ],
            'dethleffs-leal' => [
                'id' => 'dethleffs-post-01',
                'frame' => 'dethleffs',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#C0392B',           // gerçek marka rengi: kurumsal kırmızı
                'phone' => '',
                'website' => ['www.lealkaravan.com'],
                'fonts' => ['script' => 'Poppins', 'body' => 'Poppins'],
                'text_overlay' => false, // gerçek ürün fotoları temiz, sadece köşe logoları
            ],
            'novoura' => [
                'id' => 'novoura-post-01',
                'frame' => 'novoura',
                'canvas' => ['w' => 1080, 'h' => 1350],
                'brand_color' => '#FFFFFF',           // sade beyaz
                'phone' => '',
                'website' => ['novouracreative.com'],
                'fonts' => ['script' => 'Poppins', 'body' => 'Poppins'],
                'text_overlay' => false, // aktif Instagram yok; sade/minimal site diliyle tutarlı
            ],
        ];

        return $templates[$brand->slug] ?? null;
    }
}
