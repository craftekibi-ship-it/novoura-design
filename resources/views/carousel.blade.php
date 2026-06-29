<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Novoura Design · Esto Carousel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Poppins:ital,wght@0,400;0,600;0,700;1,500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<style>
  :root{ --amber:#E8943A; --bg:#0e0e10; --panel:#1a1a1d; --line:#2a2a2e; --ink:#f4f1ea; --muted:#9a978f; }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:Poppins,system-ui,sans-serif;display:flex;min-height:100vh}
  .panel{width:380px;flex:none;background:var(--panel);border-right:1px solid var(--line);padding:22px;overflow-y:auto;height:100vh}
  .stage{flex:1;display:flex;flex-direction:column;align-items:center;padding:28px;overflow:auto;gap:18px}
  h1{font-size:18px;margin:0 0 2px}
  .sub{color:var(--muted);font-size:12px;margin:0 0 18px}
  label{display:block;font-size:12px;color:var(--muted);margin:14px 0 6px}
  select,input[type=text],textarea,button{width:100%;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:10px;padding:10px 12px;font:inherit;font-size:14px}
  textarea{resize:vertical;min-height:60px}
  .btn{cursor:pointer;border:none;font-weight:600;transition:.15s}
  .btn-amber{background:var(--amber);color:#1a1a1a}
  .btn-amber:hover{filter:brightness(1.08)}
  .btn-ghost{background:#101013;border:1px solid var(--line)}
  .btn-ghost:hover{border-color:var(--amber)}
  .row{display:flex;gap:8px} .row>*{flex:1}
  .mini{font-size:11px;color:var(--muted);margin-top:6px;line-height:1.4}
  .divider{height:1px;background:var(--line);margin:18px 0}
  .badge{display:inline-block;font-size:10px;color:var(--amber);border:1px solid var(--amber);border-radius:20px;padding:2px 8px;margin-bottom:14px}
  .canvas-wrap{box-shadow:0 20px 60px rgba(0,0,0,.5);border-radius:8px;overflow:hidden}
  a{text-decoration:none}
  .nav{display:flex;gap:16px;margin:-6px 0 20px;font-size:13px;flex-wrap:wrap}
  .nav a{color:var(--muted)} .nav a.on{color:var(--amber);font-weight:600}
  .spin{display:inline-block;width:13px;height:13px;border:2px solid #0003;border-top-color:#1a1a1a;border-radius:50%;animation:s .7s linear infinite;vertical-align:-2px;margin-right:6px}
  @keyframes s{to{transform:rotate(360deg)}}
  .strip{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;max-width:760px}
  .slide-th{position:relative;width:78px;height:98px;border-radius:7px;overflow:hidden;border:2px solid var(--line);cursor:pointer;background:#000}
  .slide-th.on{border-color:var(--amber)}
  .slide-th img{width:100%;height:100%;object-fit:cover}
  .slide-th .n{position:absolute;top:2px;left:4px;font-size:10px;color:#fff;text-shadow:0 1px 2px #000}
  .slide-th .del{position:absolute;top:2px;right:3px;background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;width:16px;height:16px;font-size:11px;line-height:1;cursor:pointer;padding:0}
  .add-th{width:78px;height:98px;border-radius:7px;border:2px dashed var(--line);background:transparent;color:var(--muted);font-size:24px;cursor:pointer}
  .add-th:hover{border-color:var(--amber);color:var(--amber)}
</style>
</head>
<body>
@if(!$template)
  <div style="max-width:560px;margin:80px auto;padding:32px;background:var(--panel);border:1px solid var(--line);border-radius:14px;text-align:center">
    <div style="color:var(--amber);font-weight:700;letter-spacing:.5px;margin-bottom:10px">NOVOURA DESIGN</div>
    <h2 style="margin:0 0 8px">{{ $brand->ad }} için şablon hazır değil</h2>
    <p style="color:var(--muted);font-size:14px;line-height:1.6">Carousel için önce bu markanın post şablonu kurulmalı. Tasarımını + logosunu gönder, kuralım.</p>
    <p style="margin-top:18px"><a href="/pano" style="color:var(--amber)">← Panoya dön</a></p>
  </div>
@else
  <div class="panel">
    <span class="badge">NOVOURA DESIGN</span>
    <h1>{{ $brand->ad }} · Carousel</h1>
    <p class="sub">Çok slaytlı post. Slayt ekle → her birine foto+metin → hepsini indir / kuyruğa kaydet.</p>
    @isset($navBrands)<select onchange="if(this.value)location='/marka/'+this.value" style="width:100%;margin-bottom:14px;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:8px 10px;font:inherit;font-size:13px">@foreach($navBrands as $b)<option value="{{ $b->slug }}" @selected($b->slug===$navBrandSlug)>{{ $b->ad }}</option>@endforeach</select>@endisset
    <nav class="nav"><a href="/pano">Pano</a><a href="/studio">Stüdyo</a><a href="/toplu">Toplu</a><a href="/carousel" class="on">Carousel</a><a href="/plan">İçerik Planı</a><a href="/kuyruk">Onay Kuyruğu</a><a href="/takvim">Takvim</a><form method="POST" action="/logout" style="margin-left:auto;display:inline">@csrf<button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font:inherit;font-size:13px;padding:0">Çıkış</button></form></nav>

    <label>Yemekten AI slayt ekle</label>
    <select id="itemSelect"></select>
    <div class="row" style="margin-top:8px">
      <button id="aiAdd" class="btn btn-amber">✨ AI slayt ekle</button>
      <button id="blankAdd" class="btn btn-ghost">+ Boş slayt</button>
    </div>
    <p class="mini" id="addStatus"></p>

    <div class="divider"></div>

    <label>Aktif slayt — Başlık (beyaz)</label>
    <input type="text" id="fHeadline">
    <label>Vurgu (amber)</label>
    <input type="text" id="fEmphasis">
    <label>Etiketler (· ile ayrılır)</label>
    <input type="text" id="fTags">
    <label>Aktif slaytın fotoğrafı</label>
    <input type="file" id="photoInput" accept="image/*" class="btn-ghost">

    <div class="divider"></div>

    <label>Tüm slaytları indir</label>
    <button class="btn btn-ghost" id="exportAll">⬇ Carousel'i indir (PNG'ler)</button>

    <label>Kaydet (kuyruğa)</label>
    <div class="row">
      <button class="btn btn-ghost" id="saveDraft">Taslak</button>
      <button class="btn btn-amber" id="saveApprove">Onaya gönder ✓</button>
    </div>
    <p class="mini" id="saveStatus"></p>

    <label style="margin-top:16px">Caption (ortak)</label>
    <textarea id="fCaption" placeholder="Carousel açıklaması..."></textarea>
  </div>

  <div class="stage">
    <div class="canvas-wrap"><canvas id="c"></canvas></div>
    <div class="strip" id="strip"></div>
  </div>

<script>
  window.CFG = {
    template: @json($template),
    items: @json($items),
    csrf: "{{ csrf_token() }}",
    base: "{{ url('/studio') }}",
    postsUrl: "{{ url('/posts') }}",
    kuyrukUrl: "{{ url('/kuyruk') }}",
    demoPhoto: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80"
  };
</script>
@verbatim
<script>
const T = window.CFG.template, AMBER = T.brand_color;
const W = 1080, H = 1350, SCALE = 0.42;

const canvas = new fabric.Canvas('c', { width:W*SCALE, height:H*SCALE, backgroundColor:'#1a1a1a', preserveObjectStacking:true });
canvas.setZoom(SCALE);

let photoObj=null, overlay=null, frame=null, headlineBox, emphasisBox, tagsBox;
let slides = [];   // {cid, headline, emphasis, tags, photoSrc, thumb}
let active = -1;

function buildFrame(){
  if(T.frame === 'serm-barr') return buildFrameSermBarr();
  if(T.frame === 'vail') return buildFrameVail();
  if(T.frame === 'pureline') return buildFramePureline();
  if(T.frame === 'dethleffs') return buildFrameDethleffs();
  if(T.frame === 'novoura') return buildFrameNovoura();
  const els = [];
  els.push(new fabric.Text('Reservation', { left:70, top:70, fontFamily:'Caveat', fontWeight:700, fontSize:64, fill:'#fff' }));
  els.push(new fabric.Path('M 72 138 Q 150 120 270 134', { stroke:AMBER, strokeWidth:7, fill:'', strokeLineCap:'round' }));
  T.website.forEach((l,i)=> els.push(new fabric.Text(l, { left:72, top:150+i*26, fontFamily:'Poppins', fontWeight:400, fontSize:21, fill:'#fff' })));
  els.push(new fabric.Path('M12 2C12 5 7 7 7 13a5 5 0 0010 0c0-3-2-4-3-6 0 2-1 3-2 3 0-4 0-6 0-8z',{ fill:'#fff', scaleX:2.1, scaleY:2.1, originX:'center', left:965, top:38 }));
  els.push(new fabric.Text('ESTO', { originX:'right', left:1010, top:86, fontFamily:'Poppins', fontWeight:700, fontSize:44, charSpacing:140, fill:'#fff' }));
  els.push(new fabric.Text('RESTAURANT', { originX:'right', left:1008, top:136, fontFamily:'Poppins', fontWeight:400, fontSize:13, charSpacing:300, fill:'#fff' }));
  els.push(new fabric.Rect({ left:660, top:H-95, width:360, height:54, rx:27, ry:27, fill:'#fff' }));
  els.push(new fabric.Text('📞  ' + T.phone, { left:690, top:H-82, fontFamily:'Poppins', fontWeight:600, fontSize:24, fill:'#1a1a1a' }));
  frame = new fabric.Group(els, { selectable:false, evented:false });
  canvas.add(frame);
}
function buildFrameSermBarr(){
  const els = [];
  els.push(new fabric.Circle({ radius:24, left:0, top:0, stroke:'#fff', strokeWidth:3, fill:'' }));
  els.push(new fabric.Text('S', { left:24, top:5, originX:'center', fontFamily:'Poppins', fontWeight:700, fontSize:30, fill:'#fff' }));
  els.push(new fabric.Text('SERM & BARR', { left:64, top:13, fontFamily:'Poppins', fontWeight:600, fontSize:30, charSpacing:160, fill:'#fff' }));
  const g = new fabric.Group(els, { selectable:false, evented:false });
  g.set({ left:W/2, top:H-100, originX:'center', originY:'top' });
  frame = g; canvas.add(g);
}
function buildFrameVail(){
  const els = [];
  els.push(new fabric.Rect({ left:0, top:H-90, width:W, height:90, fill:'rgba(18,34,43,0.82)' }));
  els.push(new fabric.Path('M0 0 L26 0 L13 22 Z', { left:60, top:H-66, fill:'#fff' }));
  els.push(new fabric.Text('VAIL', { left:98, top:H-66, fontFamily:'Poppins', fontWeight:700, fontSize:36, charSpacing:140, fill:'#fff' }));
  els.push(new fabric.Rect({ left:W-290, top:H-67, width:230, height:48, rx:24, ry:24, fill:'#fff' }));
  els.push(new fabric.Text('Şimdi Keşfet', { left:W-258, top:H-56, fontFamily:'Poppins', fontWeight:600, fontSize:24, fill:'#15323F' }));
  els.push(new fabric.Rect({ left:W-220, top:40, width:180, height:62, rx:12, ry:12, fill:'rgba(18,34,43,0.82)' }));
  els.push(new fabric.Path('M0 0 L22 0 L11 18 Z', { left:W-196, top:60, fill:'#fff' }));
  els.push(new fabric.Text('VAIL', { left:W-164, top:60, fontFamily:'Poppins', fontWeight:700, fontSize:28, charSpacing:120, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false });
  canvas.add(frame);
}
function buildFramePureline(){
  const els = [];
  els.push(new fabric.Rect({ left:0, top:H-84, width:W, height:84, fill:'rgba(15,61,62,0.85)' }));
  els.push(new fabric.Text('pureline', { left:60, top:H-62, fontFamily:'Poppins', fontWeight:700, fontSize:38, fill:'#fff' }));
  els.push(new fabric.Text('Profesyonel Temizlik', { left:W-360, top:H-54, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#9fe6da' }));
  els.push(new fabric.Rect({ left:60, top:56, width:240, height:46, rx:23, ry:23, fill:'#3BD6C0' }));
  els.push(new fabric.Text('✓ Hijyen Garantili', { left:84, top:67, fontFamily:'Poppins', fontWeight:600, fontSize:22, fill:'#0F3D3E' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}
function buildFrameDethleffs(){
  const els = [];
  els.push(new fabric.Text('DETHLEFFS', { left:60, top:54, fontFamily:'Poppins', fontWeight:700, fontSize:42, charSpacing:120, fill:'#fff' }));
  els.push(new fabric.Text('by Leal Karavan', { left:64, top:106, fontFamily:'Poppins', fontWeight:500, fontSize:22, fill:'#D9BE84' }));
  els.push(new fabric.Rect({ left:0, top:H-78, width:W, height:78, fill:'rgba(14,42,71,0.80)' }));
  els.push(new fabric.Text('lealkaravan.com', { left:60, top:H-55, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#fff' }));
  els.push(new fabric.Text('Almanya kalitesi', { left:W-300, top:H-55, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#D9BE84' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}
function buildFrameNovoura(){
  const els = [];
  els.push(new fabric.Text('NOVOURA', { left:60, top:H-94, fontFamily:'Poppins', fontWeight:700, fontSize:40, charSpacing:170, fill:'#fff' }));
  els.push(new fabric.Text('CREATIVE', { left:64, top:H-48, fontFamily:'Poppins', fontWeight:400, fontSize:20, charSpacing:320, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}
function buildOverlay(){
  overlay = new fabric.Rect({ left:0, top:0, width:W, height:H, selectable:false, evented:false });
  overlay.set('fill', new fabric.Gradient({ type:'linear', coords:{x1:0,y1:0,x2:0,y2:H},
    colorStops:[{offset:0,color:'rgba(0,0,0,0.50)'},{offset:0.30,color:'rgba(0,0,0,0.05)'},{offset:0.70,color:'rgba(0,0,0,0.05)'},{offset:1,color:'rgba(0,0,0,0.70)'}] }));
  canvas.add(overlay);
}
function buildText(){
  const tTop = H - 880;
  tagsBox = new fabric.Textbox('', { left:72, top:tTop-40, width:820, fontFamily:'Poppins', fontWeight:600, fontSize:22, charSpacing:60, fill:'#fff' });
  headlineBox = new fabric.Textbox('', { left:70, top:tTop, width:820, fontFamily:'Poppins', fontWeight:600, fontSize:62, fill:'#fff', lineHeight:1.05 });
  emphasisBox = new fabric.Textbox('', { left:70, top:tTop+78, width:820, fontFamily:(T.fonts&&T.fonts.script)||'Caveat', fontWeight:700, fontSize:72, fill:AMBER });
  canvas.add(tagsBox, headlineBox, emphasisBox);
}
function setPhoto(url, isUpload){
  return new Promise(res=>{
    fabric.Image.fromURL(url, function(img){
      if(!img){ res(); return; }
      if(photoObj) canvas.remove(photoObj);
      const s = Math.max(W/img.width, H/img.height);
      img.set({ left:W/2, top:H/2, originX:'center', originY:'center', scaleX:s, scaleY:s });
      photoObj = img; canvas.add(img); restack(); canvas.requestRenderAll(); res();
    }, isUpload ? {} : { crossOrigin:'anonymous' });
  });
}
function restack(){
  if(photoObj) canvas.sendToBack(photoObj);
  if(overlay) overlay.bringToFront();
  if(frame) frame.bringToFront();
  [tagsBox,headlineBox,emphasisBox].forEach(o=>o&&o.bringToFront());
}

/* ---------- slayt durumu ---------- */
function captureThumb(){ try{ return canvas.toDataURL({format:'jpeg', quality:0.5, multiplier:0.16}); }catch(e){ return null; } }
function saveActive(){
  if(active<0) return;
  slides[active] = Object.assign(slides[active]||{}, {
    headline: headlineBox.text, emphasis: emphasisBox.text, tags: tagsBox.text,
    photoSrc: photoObj ? photoObj.getSrc() : (slides[active]&&slides[active].photoSrc) || window.CFG.demoPhoto,
    thumb: captureThumb()
  });
}
async function loadActive(){
  const s = slides[active]; if(!s) return;
  headlineBox.set('text', s.headline||''); emphasisBox.set('text', s.emphasis||''); tagsBox.set('text', s.tags||'');
  document.getElementById('fHeadline').value = s.headline||'';
  document.getElementById('fEmphasis').value = s.emphasis||'';
  document.getElementById('fTags').value = s.tags||'';
  await setPhoto(s.photoSrc || window.CFG.demoPhoto, (s.photoSrc||'').indexOf('data:')===0);
  restack(); canvas.renderAll();
  s.thumb = captureThumb();
}
async function switchTo(i){ saveActive(); active = i; await loadActive(); renderStrip(); }
async function addSlide(data){
  saveActive();
  slides.push({ cid:data.cid||null, headline:data.headline||'', emphasis:data.emphasis||'', tags:data.tags||'', photoSrc:window.CFG.demoPhoto });
  active = slides.length-1; await loadActive(); renderStrip();
}
function delSlide(i){
  if(slides.length<=1){ slides=[]; }
  slides.splice(i,1);
  if(!slides.length){ slides.push({headline:'',emphasis:'',tags:'',photoSrc:window.CFG.demoPhoto}); active=0; }
  else if(active>=slides.length) active=slides.length-1;
  loadActive(); renderStrip();
}
function renderStrip(){
  const strip = document.getElementById('strip');
  strip.innerHTML = slides.map((s,i)=>`<div class="slide-th ${i===active?'on':''}" data-i="${i}">
      ${s.thumb?`<img src="${s.thumb}">`:''}
      <span class="n">${i+1}</span>
      <button class="del" data-del="${i}" title="Sil">✕</button>
    </div>`).join('') + `<button class="add-th" id="stripAdd">+</button>`;
  strip.querySelectorAll('.slide-th').forEach(el=>el.addEventListener('click', e=>{ if(e.target.dataset.del!==undefined) return; switchTo(+el.dataset.i); }));
  strip.querySelectorAll('[data-del]').forEach(b=>b.addEventListener('click', e=>{ e.stopPropagation(); delSlide(+b.dataset.del); }));
  document.getElementById('stripAdd').addEventListener('click', ()=>addSlide({}));
}

/* ---------- AI ---------- */
async function aiAddSlide(){
  const id = document.getElementById('itemSelect').value;
  const btn = document.getElementById('aiAdd'), st = document.getElementById('addStatus');
  btn.disabled=true; btn.innerHTML='<span class="spin"></span>Üretiliyor...'; st.textContent='';
  try{
    const r = await fetch(`${window.CFG.base}/${id}/generate`, { method:'POST', headers:{'X-CSRF-TOKEN':window.CFG.csrf,'Accept':'application/json'} });
    if(!r.ok) throw new Error('AI hatası '+r.status);
    const d = await r.json();
    await addSlide({ cid:+id, headline:d.gorsel_basligi, emphasis:d.vurgu, tags:(d.one_cikan||[]).join('  ·  ') });
    const cap = document.getElementById('fCaption'); if(!cap.value && d.caption) cap.value = d.caption;
    st.textContent = '✓ Slayt eklendi (foto ekle).';
  }catch(e){ st.textContent='⚠ '+e.message; }
  btn.disabled=false; btn.innerHTML='✨ AI slayt ekle';
}

/* ---------- panel bağı ---------- */
function bindInputs(){
  [['fHeadline',()=>headlineBox],['fEmphasis',()=>emphasisBox],['fTags',()=>tagsBox]].forEach(([id,get])=>{
    document.getElementById(id).addEventListener('input', function(){ get().set('text', this.value); canvas.requestRenderAll(); });
  });
}

/* ---------- export & save ---------- */
async function renderSlideFull(s){
  headlineBox.set('text', s.headline||''); emphasisBox.set('text', s.emphasis||''); tagsBox.set('text', s.tags||'');
  await setPhoto(s.photoSrc || window.CFG.demoPhoto, (s.photoSrc||'').indexOf('data:')===0);
  restack(); canvas.renderAll();
}
async function withFullRes(fn){
  saveActive();
  canvas.setZoom(1); canvas.setDimensions({width:W,height:H});
  const out = await fn();
  canvas.setZoom(SCALE); canvas.setDimensions({width:W*SCALE,height:H*SCALE});
  await loadActive();
  return out;
}
async function exportAll(){
  await withFullRes(async ()=>{
    for(let i=0;i<slides.length;i++){
      await renderSlideFull(slides[i]);
      const url = canvas.toDataURL({format:'png', multiplier:1});
      const a=document.createElement('a'); a.href=url; a.download=`esto-carousel-${i+1}.png`; a.click();
      await new Promise(r=>setTimeout(r,250));
    }
  });
}
async function savePost(durum){
  const st = document.getElementById('saveStatus'); st.textContent='Kaydediliyor...';
  const cover = await withFullRes(async ()=>{ await renderSlideFull(slides[0]); return canvas.toDataURL({format:'png', multiplier:1}); });
  try{
    const r = await fetch(window.CFG.postsUrl, {
      method:'POST', headers:{'X-CSRF-TOKEN':window.CFG.csrf,'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({
        catalog_item_id: slides[0].cid || null,
        gorsel_yazilari: { headline:slides[0].headline, emphasis:slides[0].emphasis, tags:slides[0].tags, format:'carousel', slideCount:slides.length, slides:slides.map(s=>({headline:s.headline,emphasis:s.emphasis,tags:s.tags})) },
        caption: document.getElementById('fCaption').value, durum, image: cover
      })
    });
    if(!r.ok) throw new Error('Kayıt hatası '+r.status);
    await r.json();
    st.innerHTML = (durum==='taslak'?'✓ Taslak kaydedildi. ':'✓ Onaya gönderildi. ')+'<a style="color:var(--amber)" href="'+window.CFG.kuyrukUrl+'">Kuyruğa git →</a>';
  }catch(e){ st.textContent='⚠ '+e.message; }
}

(function init(){
  const sel=document.getElementById('itemSelect'); let lc='';
  window.CFG.items.forEach(it=>{ if(it.kategori!==lc){ const og=document.createElement('optgroup'); og.label=it.kategori||'Diğer'; sel.appendChild(og); lc=it.kategori; } const o=document.createElement('option'); o.value=it.id; o.textContent=it.ad; sel.lastChild.appendChild(o); });

  buildText(); buildOverlay(); buildFrame();
  bindInputs();
  // başlangıç: tek boş slayt
  slides=[{headline:'Slide 1',emphasis:'',tags:'',photoSrc:window.CFG.demoPhoto}]; active=0;
  loadActive().then(renderStrip);

  document.getElementById('aiAdd').addEventListener('click', aiAddSlide);
  document.getElementById('blankAdd').addEventListener('click', ()=>addSlide({}));
  document.getElementById('photoInput').addEventListener('change', e=>{ const f=e.target.files[0]; if(!f) return; const rd=new FileReader(); rd.onload=ev=>setPhoto(ev.target.result,true).then(()=>{ slides[active].photoSrc=ev.target.result; renderStrip(); }); rd.readAsDataURL(f); });
  document.getElementById('exportAll').addEventListener('click', exportAll);
  document.getElementById('saveDraft').addEventListener('click', ()=>savePost('taslak'));
  document.getElementById('saveApprove').addEventListener('click', ()=>savePost('onay_bekliyor'));

  Promise.all([document.fonts.load('600 62px Poppins'),document.fonts.load('700 46px Poppins'),document.fonts.load('700 72px Caveat')]).then(()=>canvas.requestRenderAll());
})();
</script>
@endverbatim
@endif
</body>
</html>
