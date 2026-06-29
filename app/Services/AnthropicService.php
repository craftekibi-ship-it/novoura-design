<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\CatalogItem;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Anthropic API katmanı — TÜM AI çağrıları buradan geçer.
 * Anahtar yalnızca sunucuda (config/services.php → .env), tarayıcıya asla düşmez.
 *
 * Temel prensip: metin DAİMA catalog_items.spec_json'dan beslenir.
 * Model fotoğrafa bakıp spec uydurmaz; verilen spec'i okur.
 */
class AnthropicService
{
    private string $key;
    private string $version;
    private string $smart;
    private string $fast;

    public function __construct()
    {
        $cfg = config('services.anthropic');
        $this->key = $cfg['key'] ?? '';
        $this->version = $cfg['version'];
        $this->smart = $cfg['model_smart'];
        $this->fast = $cfg['model_fast'];

        if (empty($this->key)) {
            throw new RuntimeException('ANTHROPIC_API_KEY tanımlı değil (.env).');
        }
    }

    /**
     * Düşük seviye Messages API çağrısı. Hata olursa exception fırlatır.
     */
    public function message(array $payload): array
    {
        $res = Http::withHeaders([
            'x-api-key' => $this->key,
            'anthropic-version' => $this->version,
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', $payload);

        if ($res->failed()) {
            throw new RuntimeException('Anthropic hatası: ' . $res->status() . ' ' . $res->body());
        }

        return $res->json();
    }

    /**
     * İlk text bloğunu döndürür (düz metin yanıtlar için).
     */
    private function firstText(array $response): string
    {
        foreach ($response['content'] ?? [] as $block) {
            if (($block['type'] ?? null) === 'text') {
                return $block['text'];
            }
        }
        return '';
    }

    /**
     * Bir katalog öğesi için post içeriği üretir:
     *   - gorsel_basligi : görsel-üstü kısa başlık (şablonun title kutusu)
     *   - one_cikan      : 2-3 öne çıkan kelime/özellik (features kutusu)
     *   - caption        : paylaşım açıklaması (marka sesi + spec)
     *
     * Yapılandırılmış çıktı (json_schema) ile temiz, parse edilebilir döner.
     */
    public function generatePostContent(CatalogItem $item): array
    {
        $brand = $item->brand;
        $spec = $item->spec_json ?? [];

        // Modele yalnızca GERÇEK veriyi ver (uydurma kapısı kapalı).
        $veri = [
            'ad' => $item->ad,
            'kategori' => $item->kategori,
            'aciklama' => $spec['aciklama'] ?? null,
            'malzemeler' => $spec['malzemeler'] ?? [],
            'sef_ozel' => $spec['sef_ozel'] ?? false,
            'vejetaryen' => $spec['vejetaryen'] ?? false,
            'fiyat' => $item->fiyat,
        ];

        // Görsel-üstü yazı dili markaya göre (Esto İngilizce; karavan/hizmet markaları Türkçe).
        $gorselDili = $brand->slug === 'esto' ? 'İngilizce (English)' : 'Türkçe';

        $schema = [
            'type' => 'object',
            'properties' => [
                'gorsel_basligi' => ['type' => 'string', 'description' => "{$gorselDili}. Görsel üstüne yazılacak kısa, çarpıcı başlık (ürünün/öğenin adı veya kısa bir vurgu)."],
                'vurgu' => ['type' => 'string', 'description' => "{$gorselDili}. Kısa vurgu ifadesi (2-5 kelime), yalnızca verilen spec'ten."],
                'one_cikan' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => "{$gorselDili}. 0-3 kısa öne çıkan etiket. Yalnızca verilen spec'ten.",
                ],
                'caption' => ['type' => 'string', 'description' => 'TÜRKÇE Instagram paylaşım açıklaması; marka sesiyle, kısa. Sonunda 3-5 alakalı hashtag.'],
            ],
            'required' => ['gorsel_basligi', 'vurgu', 'one_cikan', 'caption'],
            'additionalProperties' => false,
        ];

        $system = ($brand->marka_sesi_prompt ?? '')
            . "\n\nKURALLAR:\n"
            . "- Sadece sana verilen veriyi kullan. Veride/spec'te olmayan hiçbir özellik, malzeme veya iddia UYDURMA.\n"
            . "- Bilgi azsa kısa tut; boşluğu uydurmayla doldurma.\n"
            . "- GÖRSEL-ÜSTÜ yazı (gorsel_basligi, vurgu, one_cikan) {$gorselDili} olsun; kısa ve çarpıcı.\n"
            . "- CAPTION Türkçe olsun; doğal, abartısız.";

        $payload = [
            'model' => $this->smart,
            'max_tokens' => 1024,
            'system' => $system,
            'output_config' => [
                'format' => ['type' => 'json_schema', 'schema' => $schema],
            ],
            'messages' => [[
                'role' => 'user',
                'content' => "Aşağıdaki öğe için bir Instagram postu içeriği üret.\n\nVERİ (JSON):\n"
                    . json_encode($veri, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]],
        ];

        $response = $this->message($payload);
        $json = $this->firstText($response);
        $parsed = json_decode($json, true);

        if (!is_array($parsed)) {
            throw new RuntimeException('AI çıktısı çözümlenemedi: ' . $json);
        }

        return $parsed;
    }

    /**
     * Aylık içerik planı önerir. AI yalnızca verilen kataloğdan (id ile) seçer.
     * Kategori dengesi gözetir, son dönemde paylaşılmış yemekleri geri plana atar.
     *
     * @return array  [{gun, catalog_item_id, ad, kategori, tema}]
     */
    public function generateMonthlyPlan(Brand $brand, int $count, int $daysInMonth, array $recentItemIds = []): array
    {
        $catalog = $brand->catalogItems()->where('durum', 'aktif')
            ->orderBy('kategori')->orderBy('ad')
            ->get(['id', 'ad', 'kategori']);

        // Modele kompakt katalog (id | ad | kategori) — sadece bunlardan seçebilir
        $lines = $catalog->map(fn ($c) => "{$c->id} | {$c->ad} | {$c->kategori}")->implode("\n");
        $recent = implode(',', $recentItemIds) ?: 'yok';

        $schema = [
            'type' => 'object',
            'properties' => [
                'plan' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'gun' => ['type' => 'integer', 'description' => "Ayın günü (1-{$daysInMonth})."],
                            'catalog_item_id' => ['type' => 'integer', 'description' => 'Yalnızca katalog listesindeki bir id.'],
                            'tema' => ['type' => 'string', 'description' => 'ENGLISH kısa açı/tema (ör. "Weekend brunch hero", "Chef\'s signature spotlight").'],
                        ],
                        'required' => ['gun', 'catalog_item_id', 'tema'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['plan'],
            'additionalProperties' => false,
        ];

        $system = ($brand->marka_sesi_prompt ?? '')
            . "\n\nGÖREV: Esto için aylık Instagram içerik planı kur.\n"
            . "- Tam {$count} post öner.\n"
            . "- Günleri 1-{$daysInMonth} arasına dengeli yay (aynı güne birden fazla koyma).\n"
            . "- Kategorileri çeşitlendir (kahvaltı, kebap, tatlı, içecek vb. dengeli).\n"
            . "- Son dönemde paylaşılan yemekleri (id: {$recent}) MÜMKÜNSE seçme; tanıtılmamışları öne al.\n"
            . "- SADECE verilen katalog id'lerinden seç; uydurma id veya yemek YOK.\n"
            . "- 'tema' İngilizce ve kısa olsun.";

        $payload = [
            'model' => $this->smart,
            'max_tokens' => 4096,
            'system' => $system,
            'output_config' => ['format' => ['type' => 'json_schema', 'schema' => $schema]],
            'messages' => [[
                'role' => 'user',
                'content' => "KATALOG (id | ad | kategori):\n{$lines}\n\nBu aydan {$count} post için plan üret.",
            ]],
        ];

        $parsed = json_decode($this->firstText($this->message($payload)), true);
        $rows = $parsed['plan'] ?? [];

        // Doğrula: id katalogda olmalı, gün aralıkta olmalı; ad/kategori ekle
        $byId = $catalog->keyBy('id');
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['catalog_item_id'] ?? 0);
            if (!$byId->has($id)) {
                continue;
            }
            $gun = max(1, min($daysInMonth, (int) ($r['gun'] ?? 1)));
            $out[] = [
                'gun' => $gun,
                'catalog_item_id' => $id,
                'ad' => $byId[$id]->ad,
                'kategori' => $byId[$id]->kategori,
                'tema' => $r['tema'] ?? '',
            ];
        }

        usort($out, fn ($a, $b) => $a['gun'] <=> $b['gun']);
        return $out;
    }

    /**
     * Bir fotoğrafı verilen kategorilerden birine sınıflar (vision, Haiku).
     * Karavan akışı için: dis | mutfak | yatak | banyo | oturma | detay
     * Restoran akışında gerek yok (kategori katalogtan gelir).
     *
     * @param string $absolutePath  Yerel dosya yolu
     * @param string[] $categories
     */
    public function classifyImage(string $absolutePath, array $categories): string
    {
        $data = base64_encode(file_get_contents($absolutePath));
        $mime = mime_content_type($absolutePath) ?: 'image/jpeg';
        $list = implode(', ', $categories);

        $payload = [
            'model' => $this->fast,
            'max_tokens' => 20,
            'system' => "Sen bir görsel sınıflandırıcısın. Verilen fotoğrafı şu kategorilerden YALNIZCA BİRİNE ata: {$list}. "
                . "Sadece kategori adını yaz, başka hiçbir şey yazma.",
            'messages' => [[
                'role' => 'user',
                'content' => [
                    ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $mime, 'data' => $data]],
                    ['type' => 'text', 'text' => "Bu fotoğraf hangi kategori? Sadece kategori adı."],
                ],
            ]],
        ];

        $answer = strtolower(trim($this->firstText($this->message($payload))));

        // En yakın eşleşmeyi bul (model fazladan kelime yazarsa diye)
        foreach ($categories as $cat) {
            if (str_contains($answer, strtolower($cat))) {
                return $cat;
            }
        }

        return $categories[0] ?? $answer;
    }
}
