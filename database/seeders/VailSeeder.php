<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CatalogItem;
use Illuminate\Database\Seeder;

class VailSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::updateOrCreate(
            ['slug' => 'vail'],
            [
                'ad' => 'Vail Karavan',
                'tip' => 'karavan',
                'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#15323F', '#8FB9C7', '#F2EFE9'],
                'marka_sesi_prompt' =>
                    "Sen Vail Karavan'ın sosyal medya metin yazarısın. Vail, Arvi Karavan Sanayi üretimi (Serm & Barr ailesinden) çekme karavan markası. "
                    . "Ton: net, teknik-güven veren, özgürlük ve doğayla buluşma hissi; kısa-vurgulu cümleler. Slogan tarzı: 'Karavanı seçin, hayatı keşfedin'. "
                    . "ASLA spec'te olmayan teknik özellik (ölçü, yatak sayısı, donanım) uydurma; yalnızca verilen model bilgisini ve genel marka gerçeklerini kullan. Türkçe.",
            ]
        );

        $brand->catalogItems()->delete();

        // Broşür / Instagram'dan: 4 model
        $models = [
            ['ad' => 'Vail 370 E',  'kategori' => '370 Serisi'],
            ['ad' => 'Vail 370 F',  'kategori' => '370 Serisi'],
            ['ad' => 'Vail 400 SK', 'kategori' => '400 Serisi'],
            ['ad' => 'Vail 450 FL', 'kategori' => '450 Serisi'],
        ];

        foreach ($models as $m) {
            CatalogItem::create([
                'brand_id' => $brand->id,
                'tip' => 'model',
                'ad' => $m['ad'],
                'kategori' => $m['kategori'],
                'spec_json' => [
                    'uretici' => 'Arvi Karavan Sanayi A.Ş.',
                    'standart_donanim' => '8 başlık standart donanım (broşür kaynağı)',
                    'tip' => 'Çekme karavan',
                ],
            ]);
        }
    }
}
