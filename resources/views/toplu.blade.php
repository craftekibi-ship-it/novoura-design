@extends('layouts.board')
@section('title','Toplu Üretim')
@section('n_toplu','on')

@section('content')
<style>
  .bar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:22px}
  select{background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:9px;padding:9px 12px;font:inherit;font-size:14px;min-width:220px}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
  .card{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:8px}
  .card.skip{opacity:.45}
  .hd{display:flex;align-items:center;gap:8px;font-weight:600;font-size:14px}
  .hd input{width:auto}
  .card input[type=text],.card textarea{width:100%;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:7px 9px;font:inherit;font-size:13px}
  .card textarea{resize:vertical;min-height:56px}
  .lbl{font-size:11px;color:var(--muted);margin-top:2px}
  .empty{color:var(--muted);padding:30px 0}
</style>

<h1>Toplu Üretim</h1>
<p class="sub">Kategori seç → tüm yemekler için AI içerik üret → düzenle → toplu taslak kaydet. Fotoğrafları sonra "Onay Kuyruğu → Stüdyo'da aç" ile eklersin.</p>

<div class="bar">
  <select id="kat">
    @foreach($kategoriler as $k)<option value="{{ $k }}">{{ $k }}</option>@endforeach
  </select>
  <button class="btn btn-amber" id="genAll">✨ İçerik üret</button>
  <span class="muted" id="prog"></span>
  <button class="btn btn-amber" id="saveAll" style="display:none;margin-left:auto">Seçilenleri taslak kaydet</button>
</div>

<div class="grid" id="grid"><div class="empty">Bir kategori seç ve "İçerik üret"e bas.</div></div>
@endsection

@section('scripts')
<script>window.ITEMS = @json($items);</script>
<script>
const grid = document.getElementById('grid');
const prog = document.getElementById('prog');
let results = {}; // id -> {ad, headline, emphasis, tags, caption}

function itemsInCat(k){ return window.ITEMS.filter(i => (i.kategori||'') === k); }

function cardHtml(it){
  return `<div class="card" id="c-${it.id}">
    <label class="hd"><input type="checkbox" class="chk" data-id="${it.id}" checked onchange="document.getElementById('c-'+${it.id}).classList.toggle('skip', !this.checked)"> ${it.ad}</label>
    <div class="lbl">Başlık (beyaz)</div><input type="text" class="f-head">
    <div class="lbl">Vurgu (amber)</div><input type="text" class="f-emph">
    <div class="lbl">Etiketler</div><input type="text" class="f-tags">
    <div class="lbl">Caption</div><textarea class="f-cap"></textarea>
  </div>`;
}

function fill(it, d){
  const c = document.getElementById('c-'+it.id);
  c.querySelector('.f-head').value = d.gorsel_basligi || '';
  c.querySelector('.f-emph').value = d.vurgu || '';
  c.querySelector('.f-tags').value = (d.one_cikan || []).join(' · ');
  c.querySelector('.f-cap').value = d.caption || '';
  results[it.id] = { ad: it.ad };
}

async function pool(tasks, size){
  let i = 0, done = 0;
  async function worker(){ while(i < tasks.length){ const t = tasks[i++]; await t(()=>{ done++; prog.textContent = `${done}/${tasks.length} üretildi`; }); } }
  await Promise.all(Array.from({length:Math.min(size,tasks.length)}, worker));
}

document.getElementById('genAll').addEventListener('click', async ()=>{
  const k = document.getElementById('kat').value;
  const items = itemsInCat(k);
  if(!items.length){ grid.innerHTML = '<div class="empty">Bu kategoride yemek yok.</div>'; return; }
  results = {};
  grid.innerHTML = items.map(cardHtml).join('');
  document.getElementById('saveAll').style.display = 'none';
  prog.textContent = `0/${items.length} üretiliyor...`;
  const tasks = items.map(it => async (tick)=>{
    try{ const d = await api('/studio/'+it.id+'/generate','POST'); fill(it, d); }
    catch(e){ const c=document.getElementById('c-'+it.id); if(c) c.querySelector('.f-head').value='⚠ hata'; }
    tick();
  });
  await pool(tasks, 3);
  prog.textContent = `✓ ${items.length} içerik hazır — düzenle, sonra kaydet.`;
  document.getElementById('saveAll').style.display = '';
});

document.getElementById('saveAll').addEventListener('click', async ()=>{
  const checks = [...document.querySelectorAll('.chk:checked')];
  if(!checks.length){ alert('Hiç post seçilmedi'); return; }
  const btn = document.getElementById('saveAll'); btn.disabled = true;
  let saved = 0;
  for(const ch of checks){
    const id = +ch.dataset.id; const c = document.getElementById('c-'+id);
    try{
      await api('/posts','POST',{
        catalog_item_id: id,
        gorsel_yazilari: { headline: c.querySelector('.f-head').value, emphasis: c.querySelector('.f-emph').value, tags: c.querySelector('.f-tags').value },
        caption: c.querySelector('.f-cap').value,
        durum: 'taslak'
      });
      saved++; prog.textContent = `${saved}/${checks.length} kaydedildi`;
    }catch(e){ /* devam */ }
  }
  prog.innerHTML = `✓ ${saved} taslak kaydedildi. <a style="color:var(--amber)" href="/kuyruk">Onay Kuyruğu →</a>`;
  btn.disabled = false;
});
</script>
@endsection
