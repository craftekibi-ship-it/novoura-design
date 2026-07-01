@extends('layouts.board')
@section('title','Pano')
@section('n_pano','on')

@php $tipLabel = ['karavan'=>'Karavan','restoran'=>'Restoran','diger'=>'Hizmet / Diğer']; @endphp

@section('content')
<style>
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:18px}
  .card{background:var(--panel);border:1px solid var(--line);border-radius:14px;overflow:hidden;display:flex;flex-direction:column}
  .card.on{border-color:var(--amber)}
  .bar{height:8px;display:flex}
  .bar span{flex:1}
  .body{padding:16px;display:flex;flex-direction:column;gap:6px;flex:1}
  .nm{font-size:17px;font-weight:700}
  .meta{font-size:12px;color:var(--muted)}
  .st{font-size:12px;margin-top:2px}
  .st.ok{color:#6fd089}
  .st.no{color:var(--muted)}
  .acts{margin-top:auto;padding-top:12px;display:flex;gap:8px;align-items:center}
  .cur{font-size:11px;color:var(--amber);border:1px solid var(--amber);border-radius:20px;padding:2px 8px}
</style>

<h1>Markalar</h1>
<p class="sub">Çalışmak istediğin markayı seç — her marka kendi kataloğu, kuyruğu, takvimi ve planıyla ayrı çalışır.</p>

<div class="grid">
  @foreach($brands as $b)
    @php $hazir = in_array($b->slug, $templated); @endphp
    <div class="card {{ $b->slug===$current?'on':'' }}">
      <div class="bar">
        @foreach(($b->renkler ?? ['#333','#555','#777']) as $c)<span style="background:{{ $c }}"></span>@endforeach
      </div>
      <div class="body">
        <div class="nm">{{ $b->ad }}</div>
        <div class="meta">{{ $tipLabel[$b->tip] ?? $b->tip }} · {{ $b->catalog_items_count }} ürün · {{ $b->posts_count }} post</div>
        <div class="st {{ $hazir?'ok':'no' }}">{{ $hazir ? '✓ Şablon hazır' : '○ Şablon bekliyor' }}</div>
        <div class="acts">
          <a class="btn btn-amber" href="/marka/{{ $b->slug }}?open=studio">Aç →</a>
          @if($b->slug===$current)<span class="cur">aktif</span>@endif
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
