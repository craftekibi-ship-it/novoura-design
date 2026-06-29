<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CatalogItem;
use Illuminate\Database\Seeder;

class MoreBrandsSeeder extends Seeder
{
    public function run(): void
    {
        $this->pureline();
        $this->dethleffs();
        $this->novoura();

        // Sultan: restoran (katalog/şablon sonra, bilgi gelince)
        Brand::where('slug', 'sultan')->update(['tip' => 'restoran']);
    }

    private function seed(string $slug, array $brandData, array $items): void
    {
        $brand = Brand::updateOrCreate(['slug' => $slug], $brandData);
        $brand->catalogItems()->delete();
        foreach ($items as $it) {
            CatalogItem::create([
                'brand_id' => $brand->id,
                'tip' => $it['tip'] ?? 'hizmet',
                'ad' => $it['ad'],
                'kategori' => $it['kategori'],
                'spec_json' => ['aciklama' => $it['aciklama'] ?? null] + ($it['spec'] ?? []),
            ]);
        }
    }

    private function pureline(): void
    {
        $this->seed('pureline', [
            'ad' => 'Pureline Cleaning', 'tip' => 'diger', 'varsayilan_kalite' => 'sonnet',
            'renkler' => ['#0F3D3E', '#2BA89A', '#F1F7F6'],
            'marka_sesi_prompt' =>
                "Sen Pureline Cleaning'in sosyal medya metin yazarısın. Profesyonel temizlik hizmetleri markası. "
                . "Ton: güven veren, hijyen ve ferahlık vurgusu, net ve sıcak. Türkçe. Spec'te olmayan hizmet/iddia UYDURMA.",
        ], [
            ['ad' => 'Ev Temizliği', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'Detaylı ev temizliği — mutfak, banyo ve tüm yaşam alanları.'],
            ['ad' => 'Ofis Temizliği', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'İşyeri ve ofisler için periyodik profesyonel temizlik.'],
            ['ad' => 'Cam & Cephe Temizliği', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'İç-dış cam ve cephe temizliği.'],
            ['ad' => 'Halı & Koltuk Yıkama', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'Yerinde halı, koltuk ve döşeme yıkama.'],
            ['ad' => 'İnşaat Sonrası Temizlik', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'Tadilat/inşaat sonrası kaba ve ince temizlik.'],
            ['ad' => 'Dezenfeksiyon & Hijyen', 'kategori' => 'Temizlik Hizmetleri', 'aciklama' => 'Hijyen ve dezenfeksiyon uygulamaları.'],
        ]);
    }

    private function dethleffs(): void
    {
        $spec = ['spec' => ['uretici' => 'Dethleffs (Almanya)', 'distributor' => 'Leal Karavan (Türkiye distribütörü)']];
        $this->seed('dethleffs-leal', [
            'ad' => 'Dethleffs Leal', 'tip' => 'karavan', 'varsayilan_kalite' => 'sonnet',
            'renkler' => ['#0E2A47', '#C0A062', '#F2EFE9'],
            'marka_sesi_prompt' =>
                "Sen Dethleffs Leal'ın sosyal medya metin yazarısın. Dethleffs, Almanya'nın köklü karavan markası; Türkiye distribütörü Leal Karavan. "
                . "Ton: premium, Avrupa kalitesi, zarafet ve güven. Türkçe. Spec'te olmayan teknik özellik UYDURMA.",
        ], [
            ['ad' => "Dethleffs C'Joy", 'kategori' => 'Çekme Karavan', 'tip' => 'model', 'aciklama' => 'Giriş segmenti çekme karavan.'] + $spec,
            ['ad' => "Dethleffs C'GO", 'kategori' => 'Çekme Karavan', 'tip' => 'model', 'aciklama' => 'Modern yaşam alanı, fonksiyonel tasarım.'] + $spec,
            ['ad' => 'Dethleffs Camper', 'kategori' => 'Çekme Karavan', 'tip' => 'model', 'aciklama' => 'Çok yönlü, konforlu aile karavanı.'] + $spec,
            ['ad' => 'Dethleffs Beduin Scandinavia', 'kategori' => 'Premium', 'tip' => 'model', 'aciklama' => 'Üst segment, lüks donanımlı karavan.'] + $spec,
        ]);
    }

    private function novoura(): void
    {
        $this->seed('novoura', [
            'ad' => 'Novoura Creative', 'tip' => 'diger', 'varsayilan_kalite' => 'sonnet',
            'renkler' => ['#000000', '#FFFFFF', '#9A978F'],
            'marka_sesi_prompt' =>
                "Sen Novoura Creative'in sosyal medya metin yazarısın. Tasarım, yazılım ve prodüksiyon ajansı — 'Markaları büyüten tasarım, yazılım ve prodüksiyon. Tek ekip, tek çatı.' "
                . "Ton: yaratıcı, modern, sade-güçlü; trend peşinde değil kalıcı çözüm. Türkçe. Spec dışı iddia UYDURMA.",
        ], [
            ['ad' => 'Marka Kimliği', 'kategori' => 'Hizmetler', 'aciklama' => 'Logo, kurumsal kimlik, marka rehberi; kartvizit ve sosyal medya şablonları.'],
            ['ad' => 'Web Geliştirme', 'kategori' => 'Hizmetler', 'aciklama' => 'Modern, hızlı web siteleri ve uygulamalar.'],
            ['ad' => 'Çekim & Prodüksiyon', 'kategori' => 'Hizmetler', 'aciklama' => 'Fotoğraf ve video prodüksiyonu — konsepten kurguya, reklam filmi, drone.'],
            ['ad' => 'Sosyal Medya Yönetimi', 'kategori' => 'Hizmetler', 'aciklama' => 'İçerik takvimi, tasarım, çekim ve topluluk yönetimi.'],
            ['ad' => 'SEO', 'kategori' => 'Hizmetler', 'aciklama' => 'Teknik SEO, anahtar kelime stratejisi ve içerikle organik büyüme.'],
        ]);
    }
}
