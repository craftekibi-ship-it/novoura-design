<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Post;
use App\Services\AnthropicService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    private array $aylar = [1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık'];

    /** Aylık paylaşım planı PDF'i (müşteriye giden). */
    public function pdf(Request $request)
    {
        $ay = $request->query('ay', now()->format('Y-m'));
        [$y, $m] = array_map('intval', explode('-', $ay));

        $posts = Post::with('catalogItem')
            ->whereHas('brand', fn ($q) => $q->where('slug', $this->currentBrandSlug()))
            ->whereNotNull('planlanan_tarih')
            ->whereYear('planlanan_tarih', $y)
            ->whereMonth('planlanan_tarih', $m)
            ->orderBy('planlanan_tarih')
            ->get();

        // Kapakları base64 göm + caption'ı temizle (dompdf emoji basmaz)
        $posts->each(function ($p) {
            $p->cover_data = null;
            if ($p->export_yolu) {
                $path = storage_path('app/public/' . $p->export_yolu);
                if (is_file($path)) {
                    $p->cover_data = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
                }
            }
            $p->caption_clean = trim(preg_replace('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2190}-\x{21FF}\x{2B00}-\x{2BFF}]/u', '', (string) $p->caption));
        });

        $pdf = Pdf::loadView('plan-pdf', [
            'posts' => $posts,
            'ayLabel' => ($this->aylar[$m] ?? $m) . ' ' . $y,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('esto-aylik-plan-' . $ay . '.pdf');
    }

    public function show()
    {
        return view('plan', ['ay' => now()->format('Y-m')]);
    }

    /** AI aylık plan önerisi. */
    public function suggest(Request $request, AnthropicService $ai)
    {
        $v = $request->validate([
            'ay' => 'required|date_format:Y-m',
            'count' => 'required|integer|min:1|max:31',
        ]);

        $brand = $this->currentBrand();
        [$y, $m] = array_map('intval', explode('-', $v['ay']));
        $daysInMonth = Carbon::create($y, $m, 1)->daysInMonth;

        $recent = Post::whereHas('brand', fn ($q) => $q->where('slug', 'esto'))
            ->where('created_at', '>=', now()->subDays(60))
            ->pluck('catalog_item_id')->filter()->unique()->values()->all();

        $plan = $ai->generateMonthlyPlan($brand, (int) $v['count'], $daysInMonth, $recent);

        return response()->json(['plan' => $plan]);
    }

    /** Planı onayla → takvime taslak postlar oluştur. */
    public function approve(Request $request)
    {
        $v = $request->validate([
            'ay' => 'required|date_format:Y-m',
            'items' => 'required|array',
            'items.*.catalog_item_id' => 'required|integer',
            'items.*.gun' => 'required|integer',
            'items.*.tema' => 'nullable|string',
        ]);

        $brand = $this->currentBrand();
        $count = 0;

        foreach ($v['items'] as $it) {
            $tarih = Carbon::parse($v['ay'] . '-' . str_pad((string) $it['gun'], 2, '0', STR_PAD_LEFT));
            Post::create([
                'brand_id' => $brand->id,
                'catalog_item_id' => $it['catalog_item_id'],
                'durum' => 'taslak',
                'planlanan_tarih' => $tarih,
                'gorsel_yazilari_json' => ['tema' => $it['tema'] ?? null],
            ]);
            $count++;
        }

        return response()->json(['ok' => true, 'count' => $count]);
    }
}
