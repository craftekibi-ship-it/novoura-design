@extends('layouts.board')
@section('title','Görseller')
@section('n_gorseller','on')

@php
$oneriler = match($brand->tip ?? '') {
    'karavan' => ['dış','iç','yatak','mutfak','banyo','detay'],
    'restoran' => ['yemek','mekan','detay'],
    'hizmet' => ['öncesi','sonrası','ekip'],
    default => ['genel'],
};
@endphp

@section('content')
<style>
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px}
  .card{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px}
  .hd{font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:space-between}
  .cnt{font-size:12px;color:var(--muted);font-weight:400}
  .thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:6px}
  .thumb{position:relative;aspect-ratio:1;border-radius:7px;overflow:hidden;border:1px solid var(--line);background:#101013}
  .thumb img{width:100%;height:100%;object-fit:cover;display:block}
  .thumb .tag{position:absolute;left:2px;bottom:2px;font-size:9px;background:rgba(0,0,0,.65);color:#fff;padding:1px 4px;border-radius:4px}
  .thumb .del{position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:5px;background:rgba(0,0,0,.65);color:#e0857f;border:none;cursor:pointer;font-size:12px;line-height:18px;padding:0}
  .thumb .del:hover{background:#c0504a;color:#fff}
  .empty-thumbs{font-size:12px;color:var(--muted);padding:8px 0}
  .up{display:flex;gap:6px;align-items:center;border-top:1px solid var(--line);padding-top:10px}
  .up input[type=text]{background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:6px 8px;font:inherit;font-size:12px;width:90px}
  .up input[type=file]{flex:1;font-size:12px;color:var(--muted)}
  .up .lab{font-size:12px;color:var(--muted);flex-shrink:0}
</style>

<h1>Görseller</h1>
<p class="sub">{{ $brand->ad }} — her model/ürüne fotoğraf yükle. Stüdyo bu kütüphaneden fotoğraf çekecek, her seferinde elle yüklemene gerek kalmayacak.</p>

@if($items->isEmpty())
  <div class="empty-thumbs">Bu markada henüz ürün/model kaydı yok.</div>
@else
<div class="grid" id="grid">
  @foreach($items as $it)
  <div class="card" id="item-{{ $it->id }}">
    <div class="hd"><span>{{ $it->ad }} <span class="cnt">({{ $it->kategori }})</span></span><span class="cnt" id="cnt-{{ $it->id }}">{{ $it->assets->count() }} foto</span></div>
    <div class="thumbs" id="thumbs-{{ $it->id }}">
      @forelse($it->assets as $a)
        <div class="thumb" id="asset-{{ $a->id }}">
          <img src="{{ asset('storage/'.$a->dosya) }}" loading="lazy">
          @if($a->shot_type)<span class="tag">{{ $a->shot_type }}</span>@endif
          <button class="del" onclick="delAsset({{ $a->id }})">✕</button>
        </div>
      @empty
        <div class="empty-thumbs">Henüz foto yok.</div>
      @endforelse
    </div>
    <div class="up">
      <span class="lab">Yükle:</span>
      <input type="text" list="tags" placeholder="tip" id="tag-{{ $it->id }}">
      <input type="file" accept="image/*" multiple id="file-{{ $it->id }}" onchange="uploadFiles({{ $it->id }}, this.files)">
    </div>
  </div>
  @endforeach
</div>
<datalist id="tags">
  @foreach($oneriler as $o)<option value="{{ $o }}">@endforeach
</datalist>
@endif
@endsection

@section('scripts')
<script>
async function uploadFiles(itemId, files){
  const tag = document.getElementById('tag-'+itemId).value.trim();
  for(const file of files){
    const fd = new FormData();
    fd.append('catalog_item_id', itemId);
    fd.append('foto', file);
    if(tag) fd.append('shot_type', tag);
    const r = await fetch('/gorseller', { method:'POST', headers:{'X-CSRF-TOKEN':window.CSRF,'Accept':'application/json'}, body: fd });
    if(!r.ok){ alert('Yükleme hatası: ' + file.name); continue; }
    const d = await r.json();
    const thumbs = document.getElementById('thumbs-'+itemId);
    const empty = thumbs.querySelector('.empty-thumbs'); if(empty) empty.remove();
    thumbs.insertAdjacentHTML('afterbegin', `<div class="thumb" id="asset-${d.asset.id}"><img src="${d.url}"><span class="tag">${d.asset.shot_type||''}</span><button class="del" onclick="delAsset(${d.asset.id})">✕</button></div>`);
    const cnt = document.getElementById('cnt-'+itemId); cnt.textContent = (parseInt(cnt.textContent)||0)+1 + ' foto';
  }
  document.getElementById('file-'+itemId).value = '';
}

async function delAsset(id){
  if(!confirm('Bu fotoğrafı sil?')) return;
  await fetch('/gorseller/'+id, { method:'DELETE', headers:{'X-CSRF-TOKEN':window.CSRF,'Accept':'application/json'} });
  document.getElementById('asset-'+id)?.remove();
}
</script>
@endsection
