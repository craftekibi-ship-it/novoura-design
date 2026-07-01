<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Novoura Design · @yield('title')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{ --amber:#E8943A; --bg:#0e0e10; --panel:#1a1a1d; --line:#2a2a2e; --ink:#f4f1ea; --muted:#9a978f; }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:Poppins,system-ui,sans-serif}
  a{text-decoration:none;color:inherit}
  header{display:flex;align-items:center;gap:24px;padding:16px 28px;border-bottom:1px solid var(--line);position:sticky;top:0;background:var(--bg);z-index:10}
  .brand{font-weight:700}
  .brand b{color:var(--amber)}
  nav{display:flex;gap:18px;font-size:14px}
  nav a{color:var(--muted)} nav a.on{color:var(--amber);font-weight:600}
  .wrap{padding:28px;max-width:1300px;margin:0 auto}
  h1{font-size:22px;margin:0 0 4px}
  .sub{color:var(--muted);font-size:13px;margin:0 0 24px}
  .badge{display:inline-block;font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600}
  .b-taslak{background:#33333a;color:#cfcdc7}
  .b-onay_bekliyor{background:#5a3d18;color:var(--amber)}
  .b-onayli{background:#1e3d28;color:#6fd089}
  .b-planlandi{background:#1d3350;color:#74a8e6}
  .b-paylasildi{background:#3a2350;color:#b98fe6}
  .btn{cursor:pointer;border:1px solid var(--line);background:#101013;color:var(--ink);border-radius:9px;padding:7px 11px;font:inherit;font-size:13px;font-weight:600}
  .btn:hover{border-color:var(--amber)}
  .btn-amber{background:var(--amber);color:#1a1a1a;border:none}
  .btn-danger:hover{border-color:#c0504a;color:#e0857f}
  input[type=date]{background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:9px;padding:6px 9px;font:inherit;font-size:13px}
  .muted{color:var(--muted)}
</style>
</head>
<body>
  <header>
    <span class="brand">NOVOURA <b>DESIGN</b></span>
    @isset($navBrands)
      <select onchange="if(this.value)location='/marka/'+this.value" style="background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:6px 9px;font:inherit;font-size:13px">
        @foreach($navBrands as $b)<option value="{{ $b->slug }}" @selected($b->slug===$navBrandSlug)>{{ $b->ad }}</option>@endforeach
      </select>
    @endisset
    <nav>
      <a href="/pano" class="@yield('n_pano')">Pano</a>
      <a href="/gorseller" class="@yield('n_gorseller')">Görseller</a>
      <a href="/studio" class="@yield('n_studio')">Stüdyo</a>
      <a href="/toplu" class="@yield('n_toplu')">Toplu</a>
      <a href="/carousel" class="@yield('n_carousel')">Carousel</a>
      <a href="/plan" class="@yield('n_plan')">İçerik Planı</a>
      <a href="/kuyruk" class="@yield('n_kuyruk')">Onay Kuyruğu</a>
      <a href="/takvim" class="@yield('n_takvim')">Takvim</a>
    </nav>
    <form method="POST" action="/logout" style="margin-left:auto">@csrf<button class="btn" type="submit">Çıkış</button></form>
  </header>
  <div class="wrap">@yield('content')</div>
  <script>
    window.CSRF = document.querySelector('meta[name=csrf-token]').content;
    async function api(url, method, body){
      const r = await fetch(url, { method, headers:{'X-CSRF-TOKEN':window.CSRF,'Content-Type':'application/json','Accept':'application/json'}, body: body?JSON.stringify(body):undefined });
      if(!r.ok) throw new Error('Hata ' + r.status);
      return r.json();
    }
  </script>
  @yield('scripts')
</body>
</html>
