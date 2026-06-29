@extends('layouts.board')
@section('title','Onay Kuyruğu')
@section('n_kuyruk','on')

@php
  $labels = ['taslak'=>'Taslak','onay_bekliyor'=>'Onay bekliyor','onayli'=>'Onaylı','planlandi'=>'Planlandı','paylasildi'=>'Paylaşıldı'];
@endphp

@section('content')
<style>
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
  .card{background:var(--panel);border:1px solid var(--line);border-radius:14px;overflow:hidden;display:flex;flex-direction:column}
  .card .thumb{aspect-ratio:4/5;background:#000;width:100%;object-fit:cover;display:block}
  .card .body{padding:12px 14px;display:flex;flex-direction:column;gap:8px;flex:1}
  .card .name{font-weight:600;font-size:14px}
  .card .cap{font-size:12px;color:var(--muted);line-height:1.4;max-height:50px;overflow:hidden}
  .card .acts{display:flex;flex-wrap:wrap;gap:6px;margin-top:auto;padding-top:8px}
  .empty{color:var(--muted);padding:40px 0;text-align:center}
</style>

<h1>Onay Kuyruğu</h1>
<p class="sub">{{ $posts->count() }} post · onayla, planla, stüdyoda düzenle.</p>

@if($posts->isEmpty())
  <div class="empty">Henüz post yok. <a href="/studio" style="color:var(--amber)">Stüdyo'da üret →</a></div>
@else
<div class="grid">
  @foreach($posts as $p)
  <div class="card" id="card-{{ $p->id }}">
    @if($p->export_yolu)
      <img class="thumb" src="{{ asset('storage/'.$p->export_yolu) }}" alt="">
    @else
      <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:12px">📷 Foto bekliyor</div>
    @endif
    <div class="body">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
        <span class="name">{{ $p->catalogItem->ad ?? ('Carousel' ) }}
          @if(($p->gorsel_yazilari_json['format'] ?? null) === 'carousel')
            <span class="badge" style="background:#3a2350;color:#b98fe6;margin-left:4px">🎠 {{ $p->gorsel_yazilari_json['slideCount'] ?? '' }}</span>
          @endif
        </span>
        <span class="badge b-{{ $p->durum }}">{{ $labels[$p->durum] ?? $p->durum }}</span>
      </div>
      <div class="cap">{{ \Illuminate\Support\Str::limit($p->caption, 120) }}</div>
      <div class="acts">
        @if(!in_array($p->durum, ['onayli','planlandi']))
          <button class="btn btn-amber" onclick="onayla({{ $p->id }})">✓ Onayla</button>
        @endif
        <input type="date" id="d-{{ $p->id }}" value="{{ optional($p->planlanan_tarih)->format('Y-m-d') }}">
        <button class="btn" onclick="planla({{ $p->id }})">Planla</button>
        <a class="btn" href="/studio/post/{{ $p->id }}">Stüdyo'da aç</a>
        <button class="btn btn-danger" onclick="sil({{ $p->id }})">Sil</button>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif
@endsection

@section('scripts')
<script>
  async function onayla(id){ try{ await api('/posts/'+id+'/durum','POST',{durum:'onayli'}); location.reload(); }catch(e){ alert(e.message); } }
  async function planla(id){ const t=document.getElementById('d-'+id).value; try{ await api('/posts/'+id+'/planla','POST',{tarih:t}); location.reload(); }catch(e){ alert(e.message); } }
  async function sil(id){ if(!confirm('Bu post silinsin mi?'))return; try{ await api('/posts/'+id,'DELETE'); document.getElementById('card-'+id).remove(); }catch(e){ alert(e.message); } }
</script>
@endsection
