<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Novoura Design · Giriş</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{ --amber:#E8943A; --bg:#0e0e10; --panel:#1a1a1d; --line:#2a2a2e; --ink:#f4f1ea; --muted:#9a978f; }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:Poppins,system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh}
  .card{width:360px;background:var(--panel);border:1px solid var(--line);border-radius:16px;padding:30px}
  .brand{font-weight:700;letter-spacing:.5px;margin-bottom:4px}
  .brand b{color:var(--amber)}
  .sub{color:var(--muted);font-size:13px;margin:0 0 22px}
  label{display:block;font-size:12px;color:var(--muted);margin:14px 0 6px}
  input{width:100%;background:#101013;color:var(--ink);border:1px solid var(--line);border-radius:10px;padding:11px 12px;font:inherit;font-size:14px}
  input:focus{outline:none;border-color:var(--amber)}
  button{width:100%;margin-top:22px;background:var(--amber);color:#1a1a1a;border:none;border-radius:10px;padding:12px;font:inherit;font-weight:600;font-size:15px;cursor:pointer}
  .err{background:#3a1f1d;color:#e0857f;border-radius:10px;padding:9px 12px;font-size:13px;margin-bottom:8px}
  .chk{display:flex;align-items:center;gap:8px;margin-top:14px;font-size:13px;color:var(--muted)}
  .chk input{width:auto}
</style>
</head>
<body>
  <form class="card" method="POST" action="/login">
    @csrf
    <div class="brand">NOVOURA <b>DESIGN</b></div>
    <p class="sub">Esto içerik paneli · tek kullanıcı</p>

    @if($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    <label>E-posta</label>
    <input type="email" name="email" value="{{ old('email') }}" autofocus required>

    <label>Şifre</label>
    <input type="password" name="password" required>

    <label class="chk"><input type="checkbox" name="remember" value="1"> Beni hatırla</label>

    <button type="submit">Giriş yap</button>
  </form>
</body>
</html>
