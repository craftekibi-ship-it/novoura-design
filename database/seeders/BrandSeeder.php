<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'slug' => 'vail', 'ad' => 'Vail Karavan', 'tip' => 'karavan', 'varsayilan_kalite' => 'haiku',
                'renkler' => ['#1A1A1A', '#C9A24B', '#F4F1EA'],
                'marka_sesi_prompt' => 'Sen Vail Karavan\'ın sosyal medya metin yazarısın. Ton: net, teknik-güven veren, özgürlük ve yolculuk hissi uyandıran. Abartısız; ölçü, donanım ve konforu somut anlat. Türkçe. Menüde/spec\'te olmayan hiçbir özellik UYDURMA.',
            ],
            [
                'slug' => 'dethleffs-leal', 'ad' => 'Dethleffs Leal', 'tip' => 'karavan', 'varsayilan_kalite' => 'haiku',
                'renkler' => ['#0E2A47', '#C0A062', '#F2EFE9'],
                'marka_sesi_prompt' => 'Sen Dethleffs Leal\'ın sosyal medya metin yazarısın. Ton: premium, Avrupa kalitesi, konfor ve güven. Zarif ve net. Türkçe. Spec dışı özellik UYDURMA.',
            ],
            [
                'slug' => 'serm-barr', 'ad' => 'Serm & Barr', 'tip' => 'karavan', 'varsayilan_kalite' => 'haiku',
                'renkler' => ['#161616', '#B8862F', '#EFEAE2'],
                'marka_sesi_prompt' => 'Sen Serm & Barr\'ın sosyal medya metin yazarısın. Ton: modern, dinamik, yola çıkmaya davet eden. Mobil-öncelikli kısa cümleler. Türkçe. Spec dışı bilgi UYDURMA.',
            ],
            [
                'slug' => 'sultan', 'ad' => 'Sultan', 'tip' => 'diger', 'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#2A1A2E', '#D4AF37', '#F5F0E6'],
                'marka_sesi_prompt' => 'Sen Sultan markasının sosyal medya metin yazarısın. Ton: zarif, köklü, kaliteli. Türkçe. Spec dışı iddia UYDURMA.',
            ],
            [
                'slug' => 'pureline', 'ad' => 'Pureline Cleaning', 'tip' => 'diger', 'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#0F3D3E', '#3BB3A1', '#F1F7F6'],
                'marka_sesi_prompt' => 'Sen Pureline Cleaning\'in sosyal medya metin yazarısın. Ton: güven veren, hijyen ve profesyonellik vurgusu, ferah. Türkçe. Spec dışı hizmet UYDURMA.',
            ],
            [
                'slug' => 'novoura', 'ad' => 'Novoura Creative', 'tip' => 'diger', 'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#000000', '#FFFFFF', '#9A978F'],
                'marka_sesi_prompt' => 'Sen Novoura Creative\'in sosyal medya metin yazarısın. Ton: yaratıcı, modern, sade-güçlü. Türkçe. Spec dışı iddia UYDURMA.',
            ],
        ];

        foreach ($brands as $b) {
            Brand::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
