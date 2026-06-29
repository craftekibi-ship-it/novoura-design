@extends('layouts.board')
@section('title','İçerik Planı')
@section('n_plan','on')

@section('content')
<style>
  .bar{display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;margin-bottom:22px}
  .bar label{font-size:12px;color:var(--muted)}
  .bar input{display:block;margin-top:5px;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:9px;padding:8px 10px;font:inherit;font-size:14px}
  table{width:100%;border-collapse:collapse;background:var(--panel);border:1px solid var(--line);border-radius:12px;overflow:hidden}
  th,td{padding:10px 12px;text-align:left;border-bottom:1px solid var(--line);font-size:14px}
  th{font-size:12px;color:var(--muted);font-weight:600}
  tr:last-child td{border-bottom:none}
  td input{background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:6px 8px;font:inherit;font-size:13px}
  .gun{width:60px;text-align:center}
  .tema{width:100%}
  .x{cursor:pointer;color:var(--muted);border:none;background:none;font-size:16px}
  .x:hover{color:#e0857f}
  .empty{color:var(--muted);padding:30px 0}
  .approve{margin-top:18px}
</style>

<h1>İçerik Planı</h1>
<p class="sub">AI aylık plan önersin (kategori dengeli, son dönem paylaşılmamış yemekler öne). Onayla → takvime taslak olarak düşer → fotoğraf/metni sonra ekle. <span class="muted">(PDF aylık plan ileride buradan üretilecek.)</span></p>

<div class="bar">
  <label>Ay<input type="month" id="ay" value="{{ $ay }}"></label>
  <label>Post sayısı<input type="number" id="count" value="12" min="1" max="31"></label>
  <button class="btn btn-amber" id="suggestBtn">✨ AI plan öner</button>
  <span class="muted" id="prog"></span>
</div>

<div id="wrap"><div class="empty">Ay ve post sayısını seç, "AI plan öner"e bas.</div></div>
@endsection

@section('scripts')
<script>
let PLAN = [];
const wrap = document.getElementById('wrap');
const prog = document.getElementById('prog');

function render(){
  if(!PLAN.length){ wrap.innerHTML = '<div class="empty">Plan boş.</div>'; return; }
  let rows = PLAN.map((p,i)=>`<tr data-i="${i}">
    <td><input class="gun" type="number" min="1" max="31" value="${p.gun}"></td>
    <td>${p.ad}</td>
    <td class="muted">${p.kategori||''}</td>
    <td><input class="tema" type="text" value="${(p.tema||'').replace(/"/g,'&quot;')}"></td>
    <td><button class="x" title="Çıkar" onclick="this.closest('tr').remove()">✕</button></td>
  </tr>`).join('');
  wrap.innerHTML = `<table>
    <thead><tr><th>Gün</th><th>Yemek</th><th>Kategori</th><th>Tema (açı)</th><th></th></tr></thead>
    <tbody>${rows}</tbody></table>
    <button class="btn btn-amber approve" id="approveBtn">✓ Planı onayla → takvime taslak oluştur</button>`;
  document.getElementById('approveBtn').addEventListener('click', approve);
}

async function suggest(){
  const ay = document.getElementById('ay').value;
  const count = document.getElementById('count').value;
  const btn = document.getElementById('suggestBtn');
  btn.disabled = true; prog.textContent = 'AI plan kuruyor...';
  try{
    const d = await api('/plan/suggest','POST',{ ay, count: +count });
    PLAN = d.plan || [];
    render();
    prog.textContent = `✓ ${PLAN.length} önerildi — düzenle, sonra onayla.`;
  }catch(e){ prog.textContent = '⚠ ' + e.message; }
  btn.disabled = false;
}

async function approve(){
  const ay = document.getElementById('ay').value;
  const items = [].slice.call(document.querySelectorAll('tbody tr')).map(tr=>{
    const i = +tr.dataset.i;
    return { catalog_item_id: PLAN[i].catalog_item_id, gun: +tr.querySelector('.gun').value, tema: tr.querySelector('.tema').value };
  });
  if(!items.length){ alert('Plan boş'); return; }
  const btn = document.getElementById('approveBtn'); btn.disabled = true;
  try{
    const d = await api('/plan/approve','POST',{ ay, items });
    prog.innerHTML = `✓ ${d.count} taslak takvime eklendi. <a style="color:var(--amber)" href="/takvim?ay=${ay}">Takvim →</a>`;
  }catch(e){ prog.textContent = '⚠ ' + e.message; btn.disabled = false; }
}

document.getElementById('suggestBtn').addEventListener('click', suggest);
</script>
@endsection
