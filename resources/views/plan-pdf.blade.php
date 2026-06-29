<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 26px 30px; }
  * { box-sizing: border-box; }
  body { font-family: "DejaVu Sans", sans-serif; color: #2a2a2a; font-size: 12px; }
  .head { background: #1a1a1a; color: #fff; padding: 16px 20px; }
  .head .row { width: 100%; }
  .head .t { font-size: 19px; font-weight: bold; }
  .head .s { color: #E8943A; font-size: 12px; margin-top: 3px; }
  .head .brand { color: #cfcdc7; font-size: 11px; text-align: right; }
  .head .brand b { color: #E8943A; }
  .intro { color: #777; font-size: 11px; margin: 12px 2px 4px; }
  .post { width: 100%; border: 1px solid #e6e0d3; margin-top: 12px; border-radius: 6px; }
  .post td { padding: 11px; vertical-align: top; }
  .cover img { width: 128px; border-radius: 5px; }
  .ph { width: 128px; height: 160px; background: #f2ede1; color: #b3ab97; text-align: center; padding-top: 70px; border-radius: 5px; font-size: 11px; }
  .date { display: inline-block; background: #E8943A; color: #1a1a1a; font-weight: bold; padding: 3px 9px; border-radius: 4px; font-size: 11px; }
  .tag { display: inline-block; background: #efe8f5; color: #7a4fae; padding: 2px 7px; border-radius: 4px; font-size: 10px; margin-left: 4px; }
  .dish { font-size: 15px; font-weight: bold; margin: 9px 0 1px; color: #1a1a1a; }
  .tema { color: #b07e3c; font-size: 11px; font-style: italic; }
  .cap { font-size: 12px; color: #444; line-height: 1.55; margin-top: 7px; }
  .empty { color: #999; padding: 30px; text-align: center; }
  .foot { margin-top: 18px; color: #aaa; font-size: 10px; text-align: center; }
</style>
</head>
<body>
  <div class="head">
    <table class="row"><tr>
      <td>
        <div class="t">Aylık Paylaşım Planı</div>
        <div class="s">Esto Restaurant · {{ $ayLabel }}</div>
      </td>
      <td class="brand">Hazırlayan<br><b>NOVOURA DESIGN</b></td>
    </tr></table>
  </div>

  <p class="intro">Bu ay için planlanan içerikler, paylaşım tarihleri ve açıklamalarıyla aşağıdadır.</p>

  @forelse($posts as $p)
    @php $fmt = $p->gorsel_yazilari_json['format'] ?? 'post'; @endphp
    <table class="post"><tr>
      <td style="width:140px">
        @if($p->cover_data)
          <div class="cover"><img src="{{ $p->cover_data }}"></div>
        @else
          <div class="ph">Görsel<br>hazırlanıyor</div>
        @endif
      </td>
      <td>
        <span class="date">{{ $p->planlanan_tarih->format('d.m.Y') }}</span>
        @if($fmt === 'carousel')<span class="tag">Carousel · {{ $p->gorsel_yazilari_json['slideCount'] ?? '' }} slayt</span>
        @elseif($fmt === 'story')<span class="tag">Story</span>@endif
        <div class="dish">{{ $p->catalogItem->ad ?? 'Carousel' }}</div>
        @if(!empty($p->gorsel_yazilari_json['tema']))<div class="tema">{{ $p->gorsel_yazilari_json['tema'] }}</div>@endif
        @if($p->caption_clean)<div class="cap">{{ $p->caption_clean }}</div>@endif
      </td>
    </tr></table>
  @empty
    <div class="empty">Bu ay için planlanmış içerik yok.</div>
  @endforelse

  <div class="foot">Novoura Design · Otomatik içerik planı · {{ $ayLabel }}</div>
</body>
</html>
