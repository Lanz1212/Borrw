<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login — {{ $appName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--primary:#0A2540;--accent:#FF6B35;--border:#E2E8F0;--muted:#6B7A99;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#0A2540 0%,#1a3a5c 60%,#0A2540 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px;}
    h1,h2,h3{font-family:'Syne',sans-serif;}
    .lg-box{width:100%;max-width:380px;}
    .lg-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 24px 48px rgba(0,0,0,.28);}
    .iw{position:relative;}
    .iw .ic{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);}
    .iw input{padding-left:36px;}
    .fc{border:1.5px solid var(--border);border-radius:9px;padding:10px 13px;font-size:14px;font-family:'DM Sans',sans-serif;width:100%;transition:border-color .15s,box-shadow .15s;background:#fff;color:var(--primary);}
    .fc:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,107,53,.12);}
    .flbl{font-weight:500;font-size:13px;margin-bottom:5px;display:block;color:var(--primary);}
    .fgrp{margin-bottom:14px;}
    .b-acc{background:var(--accent);border:none;color:#fff;padding:12px 18px;border-radius:9px;font-size:14px;font-weight:600;cursor:pointer;width:100%;transition:background .15s;display:flex;align-items:center;justify-content:center;gap:8px;}
    .b-acc:hover{background:#ff8c5a;}
  </style>
</head>
<body>
<div class="lg-box">
  <div class="text-center mb-4">
    <div style="width:64px;height:64px;background:var(--accent);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:32px;margin-bottom:16px;box-shadow:0 8px 24px rgba(255,107,53,.4);color:#fff;">
      <i class="bi bi-nut-fill"></i>
    </div>
    <h1 style="font-size:24px;font-weight:800;color:#fff;">{{ $appName }}</h1>
    <p style="color:rgba(255,255,255,.6);font-size:14px;">Sistem Manajemen Sparepart</p>
  </div>
  <div class="lg-card">
    @if($errors->any())
      <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:8px;">
        {{ $errors->first() }}
      </div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="fgrp">
        <label class="flbl">Username</label>
        <div class="iw">
          <i class="bi bi-person ic"></i>
          <input type="text" name="username" class="fc" placeholder="Username" value="{{ old('username') }}" autocomplete="username" required>
        </div>
      </div>
      <div class="fgrp">
        <label class="flbl">Password</label>
        <div class="iw">
          <i class="bi bi-lock ic"></i>
          <input type="password" name="password" class="fc" placeholder="Password" autocomplete="current-password" required>
        </div>
      </div>
      <button type="submit" class="b-acc mt-2">
        <i class="bi bi-box-arrow-in-right"></i> Masuk Sistem
      </button>
    </form>
  </div>
</div>
</body>
</html>
