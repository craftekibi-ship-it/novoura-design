<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CatalogItem;
use Illuminate\Database\Seeder;

class EstoSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::updateOrCreate(
            ['slug' => 'esto'],
            [
                'ad' => 'Esto Restaurant',
                'tip' => 'restoran',
                'varsayilan_kalite' => 'sonnet',
                'renkler' => ['#1A1A1A', '#C9A24B', '#F6F1E7'], // koyu + altın + krem (postlardan netleşecek)
                'fontlar' => ['baslik' => null, 'govde' => null],
                'marka_sesi_prompt' =>
                    "Sen Esto Restaurant'ın sosyal medya metin yazarısın. Esto, Osmanlı–Anadolu mutfağının köklü lezzetlerini "
                    . "modern bir tabakta sunan bir restoran. Tonun: sıcak, davetkâr, iştah açıcı; abartıdan ve klişeden uzak. "
                    . "Lezzeti, malzemeyi ve pişirmeyi öne çıkar. Kısa, akıcı Türkçe cümleler kur. Emojiyi ölçülü kullan. "
                    . "'Şef Özel' yemeklerde imza/usta dokunuşu vurgusu yap. ASLA menüde olmayan içerik, malzeme veya iddia uydurma; "
                    . "yalnızca verilen spec'teki bilgiyi kullan.",
            ]
        );

        // Eski kayıtları temizle (idempotent seed)
        $brand->catalogItems()->delete();

        foreach ($this->menu() as $kategori => $items) {
            foreach ($items as $item) {
                $item = is_array($item) ? $item : ['ad' => $item];

                CatalogItem::create([
                    'brand_id'      => $brand->id,
                    'tip'           => 'menu',
                    'ad'            => $item['ad'],
                    'kategori'      => $kategori,
                    'tanitim_acisi' => ($item['sef_ozel'] ?? false) ? 'imza' : null,
                    'spec_json'     => [
                        'aciklama'   => $item['aciklama'] ?? null,
                        'malzemeler' => $item['malzemeler'] ?? [],
                        'sef_ozel'   => $item['sef_ozel'] ?? false,
                        'vejetaryen' => $item['vejetaryen'] ?? false,
                    ],
                ]);
            }
        }
    }

    /**
     * Menü — estorestaurant.com/menus'tan.
     * Not: çoğu yemekte aciklama boş; imza/öne çıkan yemeklerde standart tarif tanımı eklendi.
     * Esto'ya özgü ayrıntılar (ör. "Esto Kapalı Burger" içeriği) sahibinden gelince doldurulacak.
     */
    private function menu(): array
    {
        return [
            'Kahvaltı' => [
                '4 Peynir Croissant', 'Avokado Croissant', 'Muz Çikolatalı Croissant',
                'Kahvaltı Tacos', 'Hummuslu Ülke Ekmeği', 'Sucuk & Yumurtalı Ülke Ekmeği',
                'Krep', 'Esto Kahvaltı Tabağı', 'Fransız Toast', 'Sıcak Kahvaltı Tabağı',
                "Mac 'N' Cheese Breakfast", 'Menemen', 'Mozarella Peynirli Ülke Ekmeği',
                'Omlet', 'Poşe Yumurta', 'Islak Et & Mantar Kahvaltısı', 'Islak Et Ülke Ekmeği',
                'Islak Et Croissant',
                ['ad' => 'Geleneksel Kahvaltı (2 Kişilik)', 'aciklama' => 'İki kişilik serpme kahvaltı tabağı.'],
            ],

            'Hamur İşi' => ['Paçanga Böreği', 'Bahar Rulosu'],

            'Mezeler' => [
                'Havuç Salatası',
                ['ad' => 'Patlıcan Salatası (Abuğanuş)', 'vejetaryen' => true],
                'Soslu Patlıcan', 'Bakla', 'Haydari',
                ['ad' => 'Humus', 'vejetaryen' => true, 'aciklama' => 'Nohut, tahin, limon ve zeytinyağıyla hazırlanan klasik humus.'],
                'Karışık Meze Tabağı', 'Zeytin Ezmesi', 'Acı Biber Sos',
            ],

            'Başlangıçlar' => [
                'Mücver Tabağı', 'Cajun Tavuk Tabağı', 'Caprese Mozarella', 'Peynirli Mantar',
                'Tavuk Çorbası', 'Çıtır Levrek', 'Falafel Tabağı', 'Patates Kızartması',
                'Kalamar Kızartması', 'Hellim Tabağı', 'Mercimek Çorbası', 'Mini Lahmacun',
                'Patates Kütüğü', 'Tereyağlı Karides', 'Atıştırmalık Tabağı', 'Dolgulu Köfte',
                'Sebze Izgarası',
            ],

            'Osmanlı & Anadolu Lezzetleri' => [
                'Abuğanuş Kebabı',
                ['ad' => 'Adana Kebabı', 'aciklama' => 'Zırhla kıyılmış kuzu etinden, közde pişen acılı şiş kebap.', 'malzemeler' => ['kuzu eti', 'acı biber', 'baharat']],
                ['ad' => 'Ali Nazik Kebabı', 'aciklama' => 'Közlenmiş patlıcan ve sarımsaklı yoğurt üzerine kuşbaşı/kıyma kebap.'],
                'Sığır Güveci', 'Beyti Kebabı', 'Çentik Kebabı', 'Grile Kuzu Şiş', 'Izgara Köfte',
                ['ad' => 'Hünkar Beğendi', 'sef_ozel' => true, 'aciklama' => 'Közlenmiş patlıcan beğendi üzerine yavaş pişmiş kuzu yahni — Osmanlı saray mutfağının imza yemeği.', 'malzemeler' => ['kuzu eti', 'közlenmiş patlıcan', 'kaşar', 'tereyağı']],
                ['ad' => 'İskender Kebabı', 'aciklama' => 'Tereyağlı pide üzerine döner, domates sos ve yoğurt.'],
                'Patlıcanlı Kebap', 'Kuzu Pirzola', 'Patatesli Sote Et',
                ['ad' => 'Et & Keşkek', 'sef_ozel' => true, 'aciklama' => 'Dövme buğday ve etin saatlerce birlikte pişirildiği geleneksel düğün yemeği.'],
                'Köfte & Beğendi',
                ['ad' => 'Mutancana', 'sef_ozel' => true, 'aciklama' => 'Kuzu eti, kuru meyveler, soğan ve baharatlarla pişen tatlı-tuzlu Osmanlı yemeği.', 'malzemeler' => ['kuzu eti', 'kuru kayısı', 'kuru erik', 'badem', 'bal']],
                'Urfa Kebabı',
            ],

            'Salatalar' => [
                ['ad' => 'Roka Yeşil Salata', 'vejetaryen' => true],
                ['ad' => 'Avokado Salatası', 'vejetaryen' => true],
                'Cajun Tavuk Salatası', 'Sezar Salata', 'Falafel Salatası',
                'Izgara Keçi Peyniri Salatası',
                ['ad' => 'Gavurdağı Salatası', 'vejetaryen' => true],
                'Hellim Salatası', 'Mozarella Salatası',
                ['ad' => 'Çoban Salata', 'vejetaryen' => true],
                'Ton Balıklı Salata',
            ],

            'Testi Kebabı' => [
                ['ad' => 'Sığır Testi Kebabı', 'aciklama' => '180 gr et, biber, soğan, domates ve özel baharatlarla testide pişer; fırın patates ve pilavla servis.'],
                ['ad' => 'Tavuk Testi Kebabı', 'aciklama' => '180 gr tavuk, biber, soğan, domates ve özel baharatlarla testide pişer; fırın patates ve pilavla servis.'],
                ['ad' => 'Kuzu Testi Kebabı', 'aciklama' => '180 gr kuzu, biber, soğan, domates ve özel baharatlarla testide pişer; fırın patates ve pilavla servis.'],
            ],

            'Pizza' => [
                'Tavuk Pizza',
                ['ad' => 'Dört Peynir Pizza', 'vejetaryen' => true],
                ['ad' => 'Funghi Pizza (Mantarlı)', 'vejetaryen' => true],
                'Et Pizza', 'Karışık Pizza',
                ['ad' => 'Margarita Pizza', 'vejetaryen' => true],
            ],

            'Steak' => [
                'Arpa Tat Steak',
                ['ad' => 'Cafe de Paris Steak', 'aciklama' => '230 gr biftek, tereyağlı Cafe de Paris sosuyla.'],
                ['ad' => 'Ispanak Soslu Steak', 'aciklama' => '230 gr biftek, kremalı ıspanak sosuyla.'],
                ['ad' => 'Kremalı Sebze Soslu Steak', 'aciklama' => '230 gr biftek, kremalı sebze sosuyla.'],
                ['ad' => 'Bal Hardal Soslu Steak', 'aciklama' => '230 gr biftek, bal-hardal sosuyla.'],
                ['ad' => "Mac 'N' Cheese Soslu Steak", 'aciklama' => "230 gr biftek, Mac 'N' Cheese sosuyla."],
            ],

            'Geleneksel Pide & Pita' => [
                ['ad' => 'Peynirli Pide', 'vejetaryen' => true],
                'Tavuklu Pide', 'Lahmacun', 'Karışık Pide',
                'Ispanak Peynir Yumurta Pide',
                ['ad' => 'Ispanaklı Peynirli Pide', 'vejetaryen' => true],
                ['ad' => 'Sebzeli Mantarlı Pide', 'vejetaryen' => true],
            ],

            'Makarna' => [
                'Fettuccini Con Pollo', 'Mantı', 'Penne Arrabbiata',
                ['ad' => 'Penne Verdure', 'vejetaryen' => true],
                'Spagetti Bolognese', 'Spagetti Napolitan',
            ],

            'Karışık Izgara' => [
                ['ad' => 'Karışık Izgara (1 Kişilik)', 'aciklama' => 'Adana, tavuk kanat, ızgara tavuk, kuzu ve köfte çeşitlerinden seçki.'],
                'Karışık Izgara (2 Kişilik)', 'Karışık Izgara (3 Kişilik)', 'Karışık Izgara (4 Kişilik)',
            ],

            'Burger' => [
                'Peynirli Burger', 'Tavuk Burger',
                ['ad' => 'Esto Kapalı Burger', 'sef_ozel' => true],
                ['ad' => 'Esto Kıyılmış Sığır Burger', 'sef_ozel' => true],
            ],

            'Tavuk' => [
                'Tavuk Güveç', 'Tavuk Pirzola', 'Tavuk Şnitzel', 'Ispanak Soslu Tavuk',
                'Badem Soslu Tavuk', 'Kremalı Sebzeli Tavuk', 'Curry Soslu Tavuk',
                'Izgara Tavuk Şiş', 'Izgara Tavuk Kanat',
            ],

            'Balık & Deniz Ürünleri' => [
                ['ad' => 'Izgara Somon', 'aciklama' => 'Izgarada pişen taze somon fileto.'],
                'Ispanak Soslu Somon', 'Levrek', 'Levrek Güveç', 'Çipura',
                'Deniz Ürünleri Güveç', 'Karides Güveç',
            ],

            'Wrap' => [
                'Sığır Wrap', 'Tavuk Wrap', 'Çıtır Tavuk Wrap',
                ['ad' => 'Falafel Wrap', 'vejetaryen' => true],
            ],

            'Tatlılar' => [
                ['ad' => 'Baklava', 'aciklama' => 'Kat kat yufka, antep fıstığı ve şerbetle.'],
                ['ad' => 'Künefe', 'aciklama' => 'Kadayıf arasında eriyen peynir, sıcak servis.'],
                'Katmer', 'Sütlaç', 'Brownie', 'Çikolatalı Cheesecake', 'Çikolatalı Muzlu Krep',
                'Magnolia', 'Red Velvet',
                ['ad' => 'San Sebastian Cheesecake', 'aciklama' => 'Üstü karamelize, içi akışkan İspanyol usulü cheesecake.'],
                'Çilekli Çikolata', 'Sufle', 'Tiramisu', 'Meyve Tabağı',
            ],

            'İçecekler' => [
                'Ev Yapımı Limonata', 'Taze Sıkılmış Meyve Suyu', 'Milkshake',
                'Özel Mokteyl', 'Türk Kahvesi', 'Espresso', 'Latte', 'Sahlep',
            ],
        ];
    }
}
