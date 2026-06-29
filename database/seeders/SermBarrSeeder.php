<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CatalogItem;
use Illuminate\Database\Seeder;

class SermBarrSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::updateOrCreate(
            ['slug' => 'serm-barr'],
            [
                'ad' => 'Serm & Barr',
                'tip' => 'karavan',
                'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#161616', '#B8862F', '#EFEAE2'],
                'marka_sesi_prompt' =>
                    "Sen Serm & Barr'ın sosyal medya metin yazarısın. Serm & Barr, Türkiye'nin TSE belgeli ilk ve tek çekme karavan üreticisi; "
                    . "European Norm bileşenler ve 4+4 yıl su sızdırmazlık garantisi sunar. Slogan: 'Sizi ilham veren yolculuklara çıkarmak'. "
                    . "Ton: modern, güven veren, özgürlük ve yolculuk hissi uyandıran; kısa-vurgulu cümleler. ASLA spec'te olmayan teknik özellik (ölçü, yatak, donanım) uydurma; "
                    . "yalnızca verilen model/seri bilgisini ve markanın genel gerçeklerini (TSE, European Norm, 4+4 garanti) kullan.",
            ]
        );

        $brand->catalogItems()->delete();

        // sermbarr.com'dan: 3 seri, 9 model
        $models = [
            'xBarr' => ['500 FUW', '580 FUDW', '600 QUW', '620 QUW'],   // premium
            'cBarr' => ['450 FSW', '530 FKSW', '540 EU'],               // kompakt
            'eBarr' => ['420 SK', '450 MKS'],                           // aile
        ];

        foreach ($models as $seri => $kodlar) {
            foreach ($kodlar as $kod) {
                CatalogItem::create([
                    'brand_id' => $brand->id,
                    'tip' => 'model',
                    'ad' => "{$seri} {$kod}",
                    'kategori' => $seri,
                    'spec_json' => [
                        'seri' => $seri,
                        'layout_kodu' => $kod,
                        'sertifika' => 'TSE belgeli + European Norm bileşenler',
                        'garanti' => '4+4 yıl su sızdırmazlık garantisi',
                    ],
                ]);
            }
        }
    }
}
