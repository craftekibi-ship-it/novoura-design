<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Novoura Design · Esto Stüdyo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Poppins:ital,wght@0,400;0,600;0,700;1,500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<style>
  :root{ --amber:#E8943A; --bg:#0e0e10; --panel:#1a1a1d; --line:#2a2a2e; --ink:#f4f1ea; --muted:#9a978f; }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:Poppins,system-ui,sans-serif;display:flex;min-height:100vh}
  .panel{width:380px;flex:none;background:var(--panel);border-right:1px solid var(--line);padding:22px;overflow-y:auto;height:100vh}
  .stage{flex:1;display:flex;align-items:flex-start;justify-content:center;padding:32px;overflow:auto}
  h1{font-size:18px;margin:0 0 2px}
  .sub{color:var(--muted);font-size:12px;margin:0 0 20px}
  label{display:block;font-size:12px;color:var(--muted);margin:16px 0 6px}
  select,input[type=text],textarea,button{width:100%;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:10px;padding:10px 12px;font:inherit;font-size:14px}
  textarea{resize:vertical;min-height:64px}
  .btn{cursor:pointer;border:none;font-weight:600;transition:.15s}
  .btn-amber{background:var(--amber);color:#1a1a1a}
  .btn-amber:hover{filter:brightness(1.08)}
  .btn-ghost{background:#101013;border:1px solid var(--line)}
  .btn-ghost:hover{border-color:var(--amber)}
  .fmt.on{border-color:var(--amber);color:var(--amber)}
  .row{display:flex;gap:8px}
  .row>*{flex:1}
  .mini{font-size:11px;color:var(--muted);margin-top:6px;line-height:1.4}
  .divider{height:1px;background:var(--line);margin:20px 0}
  .canvas-wrap{box-shadow:0 20px 60px rgba(0,0,0,.5);border-radius:8px;overflow:hidden}
  .badge{display:inline-block;font-size:10px;color:var(--amber);border:1px solid var(--amber);border-radius:20px;padding:2px 8px;margin-bottom:14px}
  .spin{display:inline-block;width:13px;height:13px;border:2px solid #0003;border-top-color:#1a1a1a;border-radius:50%;animation:s .7s linear infinite;vertical-align:-2px;margin-right:6px}
  @keyframes s{to{transform:rotate(360deg)}}
  a{text-decoration:none}
  .nav{display:flex;gap:16px;margin:-6px 0 20px;font-size:13px}
  .nav a{color:var(--muted)} .nav a.on{color:var(--amber);font-weight:600}
</style>
</head>
<body>
@if(!$template)
  <div style="max-width:560px;margin:80px auto;padding:32px;background:var(--panel);border:1px solid var(--line);border-radius:14px;text-align:center">
    <div style="color:var(--amber);font-weight:700;letter-spacing:.5px;margin-bottom:10px">NOVOURA DESIGN</div>
    @isset($navBrands)<select onchange="if(this.value)location='/marka/'+this.value" style="margin-bottom:18px;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:8px 12px;font:inherit;font-size:13px">@foreach($navBrands as $b)<option value="{{ $b->slug }}" @selected($b->slug===$navBrandSlug)>{{ $b->ad }}</option>@endforeach</select>@endisset
    <h2 style="margin:0 0 8px">{{ $brand->ad }} için şablon hazır değil</h2>
    <p style="color:var(--muted);font-size:14px;line-height:1.6">Bu markanın post şablonunu kurmak için tasarımını (örnek postlar) ve logosunu gönder — Esto'da yaptığımız gibi birebir kuralım. O zamana kadar bu marka için stüdyo kapalı.</p>
    <p style="margin-top:18px"><a href="/pano" style="color:var(--amber)">← Panoya dön</a></p>
  </div>
@else
  <div class="panel">
    <span class="badge">NOVOURA DESIGN</span>
    <h1>{{ $brand->ad }} · Stüdyo</h1>
    <p class="sub">Katalog → AI → onaylı post.</p>
    @isset($navBrands)<select onchange="if(this.value)location='/marka/'+this.value" style="width:100%;margin-bottom:14px;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:8px 10px;font:inherit;font-size:13px">@foreach($navBrands as $b)<option value="{{ $b->slug }}" @selected($b->slug===$navBrandSlug)>{{ $b->ad }}</option>@endforeach</select>@endisset
    <nav class="nav"><a href="/pano">Pano</a><a href="/studio" class="on">Stüdyo</a><a href="/toplu">Toplu</a><a href="/carousel">Carousel</a><a href="/plan">İçerik Planı</a><a href="/kuyruk">Onay Kuyruğu</a><a href="/takvim">Takvim</a><form method="POST" action="/logout" style="margin-left:auto;display:inline">@csrf<button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font:inherit;font-size:13px;padding:0">Çıkış</button></form></nav>

    <label>Format</label>
    <div class="row">
      <button class="btn btn-ghost fmt on" data-fmt="post">Post 4:5</button>
      <button class="btn btn-ghost fmt" data-fmt="story">Story 9:16</button>
    </div>

    <label>Katalogdan seç</label>
    <select id="itemSelect"></select>

    <button id="genBtn" class="btn btn-amber" style="margin-top:14px">✨ AI ile içerik üret</button>
    <p class="mini" id="genStatus"></p>

    <div class="divider"></div>

    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
      <input type="checkbox" id="fTextOverlay" style="width:auto"> Görsel üstünde yazı göster
    </label>
    <p class="mini">Kapalıysa fotoğraf temiz kalır, sadece marka filigranı görünür — metin yine de caption'a gider.</p>

    <label>Görsel başlığı (beyaz)</label>
    <input type="text" id="fHeadline" placeholder="Warm künefe,">
    <label>Vurgu (amber, el yazısı)</label>
    <input type="text" id="fEmphasis" placeholder="crowned with pistachio.">
    <label>Etiketler (· ile ayrılır)</label>
    <input type="text" id="fTags" placeholder="Chef's Special · Slow-cooked">

    <div class="divider"></div>

    <label>Fotoğraf yükle</label>
    <input type="file" id="photoInput" accept="image/*" class="btn-ghost">
    <p class="mini">Fotoyu tuval üzerinde sürükle / köşeden büyüt-küçült (kırpma).</p>

    <div class="divider"></div>

    <label>Kaydet (kuyruğa)</label>
    <div class="row">
      <button class="btn btn-ghost" id="saveDraft">Taslak</button>
      <button class="btn btn-amber" id="saveApprove">Onaya gönder ✓</button>
    </div>
    <p class="mini" id="saveStatus"></p>

    <div class="divider"></div>

    <label>Dışa aktar (PNG)</label>
    <div class="row">
      <button class="btn btn-ghost" onclick="exportPng(1)">1x</button>
      <button class="btn btn-ghost" onclick="exportPng(2)">2x</button>
    </div>

    <label style="margin-top:16px">Caption (paylaşım açıklaması)</label>
    <textarea id="fCaption" placeholder="AI üretince burada görünür..."></textarea>
    <button class="btn btn-ghost" style="margin-top:8px" onclick="copyCaption()">📋 Caption'ı kopyala</button>
  </div>

  <div class="stage"><div class="canvas-wrap"><canvas id="c"></canvas></div></div>

<script>
  window.ESTO = {
    template: @json($template),
    items: @json($items),
    currentId: @json($item?->id),
    post: @json($post ? ['id' => $post->id, 'gorsel' => $post->gorsel_yazilari_json, 'caption' => $post->caption] : null),
    csrf: "{{ csrf_token() }}",
    base: "{{ url('/studio') }}",
    postsUrl: "{{ url('/posts') }}",
    kuyrukUrl: "{{ url('/kuyruk') }}",
    demoPhoto: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80"
  };
</script>
@verbatim
<script>
const T = window.ESTO.template;
const AMBER = T.brand_color;
const FORMATS = { post:{w:1080,h:1350}, story:{w:1080,h:1920} };
let currentFormat = 'post';
let W = FORMATS.post.w, H = FORMATS.post.h;
let textOverlayOn = T.text_overlay !== false;

function fitScale(h){ return Math.min(0.5, 700/h); }
let SCALE = fitScale(H);

const canvas = new fabric.Canvas('c', {
  width: W * SCALE, height: H * SCALE, backgroundColor: '#1a1a1a', preserveObjectStacking: true
});
canvas.setZoom(SCALE);

let photoObj = null, overlay = null, frame = null;
let headlineBox, emphasisBox, tagsBox;

/* ---------- frame (sabit marka öğeleri; konum H'ye göre) ---------- */
function buildFrame(){
  if(T.frame === 'serm-barr') return buildFrameSermBarr();
  if(T.frame === 'vail') return buildFrameVail();
  if(T.frame === 'pureline') return buildFramePureline();
  if(T.frame === 'dethleffs') return buildFrameDethleffs();
  if(T.frame === 'novoura') return buildFrameNovoura();
  const els = [];

  // sol üst: Reservation + amber swoosh + site satırları
  els.push(new fabric.Text('Reservation', { left:70, top:70, fontFamily:'Caveat', fontWeight:700, fontSize:64, fill:'#fff' }));
  els.push(new fabric.Path('M 72 138 Q 150 120 270 134', { stroke:AMBER, strokeWidth:7, fill:'', strokeLineCap:'round' }));
  T.website.forEach((line,i)=> els.push(new fabric.Text(line, { left:72, top:150 + i*26, fontFamily:'Poppins', fontWeight:400, fontSize:21, fill:'#fff' })));

  // sağ üst: ESTO logo
  els.push(new fabric.Path('M12 2C12 5 7 7 7 13a5 5 0 0010 0c0-3-2-4-3-6 0 2-1 3-2 3 0-4 0-6 0-8z',
    { fill:'#fff', scaleX:2.1, scaleY:2.1, originX:'center', left:965, top:38 }));
  els.push(new fabric.Text('ESTO', { originX:'right', left:1010, top:86, fontFamily:'Poppins', fontWeight:700, fontSize:44, charSpacing:140, fill:'#fff' }));
  els.push(new fabric.Text('RESTAURANT', { originX:'right', left:1008, top:136, fontFamily:'Poppins', fontWeight:400, fontSize:13, charSpacing:300, fill:'#fff' }));

  // sağ alt: telefon rozeti (H'ye göre)
  els.push(new fabric.Rect({ left:660, top:H-95, width:360, height:54, rx:27, ry:27, fill:'#fff' }));
  els.push(new fabric.Text('📞  ' + T.phone, { left:690, top:H-82, fontFamily:'Poppins', fontWeight:600, fontSize:24, fill:'#1a1a1a' }));

  // imza ok doodle (metnin altına işaret eder)
  els.push(new fabric.Path('M 80 30 Q 10 70 60 130 M 60 130 L 40 108 M 60 130 L 84 116',
    { left:120, top:(H-880)+290, stroke:'#fff', strokeWidth:6, fill:'', strokeLineCap:'round', strokeLineJoin:'round' }));

  frame = new fabric.Group(els, { selectable:false, evented:false });
  canvas.add(frame);
}

/* Serm & Barr çerçevesi — sade, foto öncelikli: altta ortada S + SERM & BARR */
function buildFrameSermBarr(){
  const els = [];
  els.push(new fabric.Circle({ radius:24, left:0, top:0, stroke:'#fff', strokeWidth:3, fill:'' }));
  els.push(new fabric.Text('S', { left:24, top:5, originX:'center', fontFamily:'Poppins', fontWeight:700, fontSize:30, fill:'#fff' }));
  els.push(new fabric.Text('SERM & BARR', { left:64, top:13, fontFamily:'Poppins', fontWeight:600, fontSize:30, charSpacing:160, fill:'#fff' }));
  const g = new fabric.Group(els, { selectable:false, evented:false });
  g.set({ left:W/2, top:H-100, originX:'center', originY:'top' });
  frame = g; canvas.add(g);
}

/* Vail çerçevesi — alt koyu bar (▽ VAIL + Şimdi Keşfet) + sağ üst rozet */
function buildFrameVail(){
  const els = [];
  // alt bar
  els.push(new fabric.Rect({ left:0, top:H-90, width:W, height:90, fill:'rgba(18,34,43,0.82)' }));
  els.push(new fabric.Path('M0 0 L26 0 L13 22 Z', { left:60, top:H-66, fill:'#fff' }));
  els.push(new fabric.Text('VAIL', { left:98, top:H-66, fontFamily:'Poppins', fontWeight:700, fontSize:36, charSpacing:140, fill:'#fff' }));
  els.push(new fabric.Rect({ left:W-290, top:H-67, width:230, height:48, rx:24, ry:24, fill:'#fff' }));
  els.push(new fabric.Text('Şimdi Keşfet', { left:W-258, top:H-56, fontFamily:'Poppins', fontWeight:600, fontSize:24, fill:'#15323F' }));
  // sağ üst rozet
  els.push(new fabric.Rect({ left:W-220, top:40, width:180, height:62, rx:12, ry:12, fill:'rgba(18,34,43,0.82)' }));
  els.push(new fabric.Path('M0 0 L22 0 L11 18 Z', { left:W-196, top:60, fill:'#fff' }));
  els.push(new fabric.Text('VAIL', { left:W-164, top:60, fontFamily:'Poppins', fontWeight:700, fontSize:28, charSpacing:120, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false });
  canvas.add(frame);
}

/* Pureline — sade/kurumsal: alt lacivert bar + sol üst yeşil hijyen rozeti (gerçek site paleti) */
function buildFramePureline(){
  const els = [];
  els.push(new fabric.Rect({ left:0, top:H-84, width:W, height:84, fill:'rgba(13,79,124,0.85)' })); // lacivert #0D4F7C
  els.push(new fabric.Text('pureline', { left:60, top:H-62, fontFamily:'Poppins', fontWeight:700, fontSize:38, fill:'#fff' }));
  els.push(new fabric.Text('Profesyonel Temizlik', { left:W-360, top:H-54, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#8FD3F0' })); // açık mavi
  els.push(new fabric.Rect({ left:60, top:56, width:240, height:46, rx:23, ry:23, fill:'#3A8C3F' })); // yeşil
  els.push(new fabric.Text('✓ Hijyen Garantili', { left:84, top:67, fontFamily:'Poppins', fontWeight:600, fontSize:22, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}

/* Dethleffs Leal — gerçek marka rengi kurumsal kırmızı: sol üst wordmark + alt kırmızı bar */
function buildFrameDethleffs(){
  const els = [];
  els.push(new fabric.Text('DETHLEFFS', { left:60, top:54, fontFamily:'Poppins', fontWeight:700, fontSize:42, charSpacing:120, fill:'#fff' }));
  els.push(new fabric.Text('by Leal Karavan', { left:64, top:106, fontFamily:'Poppins', fontWeight:500, fontSize:22, fill:'rgba(255,255,255,0.85)' }));
  els.push(new fabric.Rect({ left:0, top:H-78, width:W, height:78, fill:'rgba(192,57,43,0.85)' })); // kurumsal kırmızı #C0392B
  els.push(new fabric.Text('lealkaravan.com', { left:60, top:H-55, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#fff' }));
  els.push(new fabric.Text('Ailenizin Dostu', { left:W-300, top:H-55, fontFamily:'Poppins', fontWeight:500, fontSize:24, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}

/* Novoura — sade siyah-beyaz ajans: sol altta wordmark */
function buildFrameNovoura(){
  const els = [];
  els.push(new fabric.Text('NOVOURA', { left:60, top:H-94, fontFamily:'Poppins', fontWeight:700, fontSize:40, charSpacing:170, fill:'#fff' }));
  els.push(new fabric.Text('CREATIVE', { left:64, top:H-48, fontFamily:'Poppins', fontWeight:400, fontSize:20, charSpacing:320, fill:'#fff' }));
  frame = new fabric.Group(els, { selectable:false, evented:false }); canvas.add(frame);
}

function buildOverlay(){
  overlay = new fabric.Rect({ left:0, top:0, width:W, height:H, selectable:false, evented:false });
  overlay.set('fill', new fabric.Gradient({
    type:'linear', coords:{x1:0,y1:0,x2:0,y2:H},
    colorStops:[
      {offset:0, color:'rgba(0,0,0,0.50)'},
      {offset:0.30, color:'rgba(0,0,0,0.05)'},
      {offset:0.70, color:'rgba(0,0,0,0.05)'},
      {offset:1, color:'rgba(0,0,0,0.70)'}
    ]
  }));
  canvas.add(overlay);
}

function buildText(){
  const tTop = H - 880; // başlık taban çizgisi (post:470, story:1040)
  tagsBox = new fabric.Textbox("Chef's Special · Slow-cooked", { left:72, top:tTop-40, width:820, fontFamily:'Poppins', fontWeight:600, fontSize:22, charSpacing:60, fill:'#fff' });
  headlineBox = new fabric.Textbox('Warm künefe,', { left:70, top:tTop, width:820, fontFamily:'Poppins', fontWeight:600, fontSize:62, fill:'#fff', lineHeight:1.05 });
  emphasisBox = new fabric.Textbox('crowned with pistachio.', { left:70, top:tTop+78, width:820, fontFamily:(T.fonts&&T.fonts.script)||'Caveat', fontWeight:700, fontSize:72, fill:AMBER });
  canvas.add(tagsBox, headlineBox, emphasisBox);
}

function setPhoto(url, isUpload){
  fabric.Image.fromURL(url, function(img){
    if(!img) return;
    if(photoObj) canvas.remove(photoObj);
    const s = Math.max(W/img.width, H/img.height);
    img.set({ left:W/2, top:H/2, originX:'center', originY:'center', scaleX:s, scaleY:s });
    photoObj = img;
    canvas.add(img);
    restack();
    canvas.requestRenderAll();
  }, isUpload ? {} : { crossOrigin:'anonymous' });
}

function restack(){
  if(photoObj) canvas.sendToBack(photoObj);
  if(overlay) overlay.bringToFront();
  if(frame) frame.bringToFront();
  [tagsBox, headlineBox, emphasisBox].forEach(o=>o && o.bringToFront());
  applyTextOverlayVisibility();
}

/* metin-üstü-görsel aç/kapa — kapalıyken temiz foto + sadece marka filigranı kalır */
function applyTextOverlayVisibility(){
  [headlineBox, emphasisBox, tagsBox, overlay].forEach(o=>o && o.set('visible', textOverlayOn));
  canvas.requestRenderAll();
}

/* ---------- format değiştir (metin+foto korunur) ---------- */
function applyFormat(fmt){
  if(fmt === currentFormat) return;
  const f = FORMATS[fmt]; currentFormat = fmt;
  const keep = { h: headlineBox.text, e: emphasisBox.text, t: tagsBox.text, cap: document.getElementById('fCaption').value };
  const src = photoObj ? photoObj.getSrc() : null;

  W = f.w; H = f.h; SCALE = fitScale(H);
  canvas.clear();
  canvas.backgroundColor = '#1a1a1a';
  canvas.setZoom(SCALE); canvas.setDimensions({ width:W*SCALE, height:H*SCALE });
  photoObj = overlay = frame = null;

  buildText(); buildOverlay(); buildFrame();
  headlineBox.set('text', keep.h); emphasisBox.set('text', keep.e); tagsBox.set('text', keep.t);
  if(src) setPhoto(src, src.indexOf('data:')===0); else setPhoto(window.ESTO.demoPhoto, false);
  restack(); canvas.requestRenderAll();
}

/* ---------- panel <-> canvas ---------- */
function bindInputs(){
  [['fHeadline',()=>headlineBox],['fEmphasis',()=>emphasisBox],['fTags',()=>tagsBox]].forEach(([id,get])=>{
    document.getElementById(id).addEventListener('input', function(){ get().set('text', this.value); canvas.requestRenderAll(); });
  });
}
function syncInputsFromCanvas(){
  document.getElementById('fHeadline').value = headlineBox.text;
  document.getElementById('fEmphasis').value = emphasisBox.text;
  document.getElementById('fTags').value = tagsBox.text;
}

async function generate(){
  const id = document.getElementById('itemSelect').value;
  const btn = document.getElementById('genBtn'), st = document.getElementById('genStatus');
  btn.disabled = true; btn.innerHTML = '<span class="spin"></span>Üretiliyor...'; st.textContent = '';
  try{
    const r = await fetch(`${window.ESTO.base}/${id}/generate`, { method:'POST', headers:{'X-CSRF-TOKEN':window.ESTO.csrf,'Accept':'application/json'} });
    if(!r.ok) throw new Error('AI hatası ' + r.status);
    const d = await r.json();
    headlineBox.set('text', d.gorsel_basligi || '');
    emphasisBox.set('text', d.vurgu || '');
    tagsBox.set('text', (d.one_cikan || []).join('  ·  '));
    document.getElementById('fCaption').value = d.caption || '';
    syncInputsFromCanvas(); canvas.requestRenderAll();
    st.textContent = '✓ Üretildi — düzenleyebilirsin.';
  }catch(e){ st.textContent = '⚠ ' + e.message; }
  btn.disabled = false; btn.innerHTML = '✨ AI ile içerik üret';
}

function exportPng(mult){
  canvas.discardActiveObject();
  canvas.setZoom(1); canvas.setDimensions({width:W,height:H});
  const url = canvas.toDataURL({ format:'png', multiplier:mult });
  canvas.setZoom(SCALE); canvas.setDimensions({width:W*SCALE,height:H*SCALE});
  const a = document.createElement('a'); a.href = url; a.download = `esto-${currentFormat}-${mult}x.png`; a.click();
}
function copyCaption(){ navigator.clipboard.writeText(document.getElementById('fCaption').value); }

async function savePost(durum){
  const id = document.getElementById('itemSelect').value;
  const st = document.getElementById('saveStatus');
  st.textContent = 'Kaydediliyor...';
  canvas.discardActiveObject();
  canvas.setZoom(1); canvas.setDimensions({width:W,height:H});
  const image = canvas.toDataURL({ format:'png', multiplier:1 });
  canvas.setZoom(SCALE); canvas.setDimensions({width:W*SCALE,height:H*SCALE}); canvas.requestRenderAll();
  try{
    const r = await fetch(window.ESTO.postsUrl, {
      method:'POST',
      headers:{'X-CSRF-TOKEN':window.ESTO.csrf,'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({
        id: (window.ESTO.post && window.ESTO.post.id) || null,
        catalog_item_id: id,
        gorsel_yazilari: { headline: headlineBox.text, emphasis: emphasisBox.text, tags: tagsBox.text, format: currentFormat, text_overlay: textOverlayOn },
        caption: document.getElementById('fCaption').value,
        durum: durum,
        image: image
      })
    });
    if(!r.ok) throw new Error('Kayıt hatası ' + r.status);
    const d = await r.json();
    if(d.id){ window.ESTO.post = window.ESTO.post || {}; window.ESTO.post.id = d.id; }
    st.innerHTML = (durum==='taslak'?'✓ Taslak kaydedildi. ':'✓ Onaya gönderildi. ') +
      '<a style="color:var(--amber)" href="'+window.ESTO.kuyrukUrl+'">Kuyruğa git →</a>';
  }catch(e){ st.textContent = '⚠ ' + e.message; }
}

(function init(){
  const sel = document.getElementById('itemSelect');
  let lastCat = '';
  window.ESTO.items.forEach(it=>{
    if(it.kategori !== lastCat){ const og=document.createElement('optgroup'); og.label=it.kategori||'Diğer'; sel.appendChild(og); lastCat=it.kategori; }
    const o=document.createElement('option'); o.value=it.id; o.textContent=it.ad;
    if(it.id===window.ESTO.currentId) o.selected=true;
    sel.lastChild.appendChild(o);
  });

  buildText(); buildOverlay(); buildFrame();
  setPhoto(window.ESTO.demoPhoto, false);
  bindInputs(); syncInputsFromCanvas();

  if(window.ESTO.post && window.ESTO.post.gorsel){
    var g = window.ESTO.post.gorsel;
    if(g.headline!=null) headlineBox.set('text', g.headline);
    if(g.emphasis!=null) emphasisBox.set('text', g.emphasis);
    if(g.tags!=null) tagsBox.set('text', g.tags);
    if(g.text_overlay!=null) textOverlayOn = !!g.text_overlay;
    document.getElementById('fCaption').value = window.ESTO.post.caption || '';
    syncInputsFromCanvas(); canvas.requestRenderAll();
  }
  document.getElementById('fTextOverlay').checked = textOverlayOn;
  document.getElementById('fTextOverlay').addEventListener('change', function(){
    textOverlayOn = this.checked; applyTextOverlayVisibility();
  });
  restack();

  document.getElementById('genBtn').addEventListener('click', generate);
  document.getElementById('photoInput').addEventListener('change', e=>{
    const f=e.target.files[0]; if(!f) return;
    const rd=new FileReader(); rd.onload=ev=>setPhoto(ev.target.result, true); rd.readAsDataURL(f);
  });
  document.getElementById('saveDraft').addEventListener('click', ()=>savePost('taslak'));
  document.getElementById('saveApprove').addEventListener('click', ()=>savePost('onay_bekliyor'));
  document.querySelectorAll('.fmt').forEach(b=>b.addEventListener('click', function(){
    document.querySelectorAll('.fmt').forEach(x=>x.classList.remove('on'));
    this.classList.add('on'); applyFormat(this.dataset.fmt);
  }));

  Promise.all([
    document.fonts.load('600 62px Poppins'),
    document.fonts.load('700 46px Poppins'),
    document.fonts.load('700 72px Caveat')
  ]).then(()=>canvas.requestRenderAll());
})();
</script>
@endverbatim
@endif
</body>
</html>
