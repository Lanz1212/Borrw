<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — {{ $appName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:#F8FAFC;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;-webkit-font-smoothing:antialiased;}
    .wrap{width:100%;max-width:360px;}
    .brand{text-align:center;margin-bottom:28px;}
    .brand-icon{width:44px;height:44px;background:#F97316;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;color:#fff;margin-bottom:14px;}
    .brand-name{font-size:20px;font-weight:700;color:#0F172A;letter-spacing:-.02em;}
    .brand-sub{font-size:13px;color:#64748B;margin-top:3px;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:10px;padding:28px;}
    .fgrp{margin-bottom:16px;}
    .flbl{font-size:12.5px;font-weight:500;color:#374151;margin-bottom:5px;display:block;}
    .iw{position:relative;}
    .iw .ic{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94A3B8;font-size:14px;}
    .fc{border:1px solid #E2E8F0;border-radius:7px;padding:9px 12px 9px 34px;font-size:13.5px;font-family:inherit;width:100%;transition:border-color .12s,box-shadow .12s;background:#fff;color:#0F172A;}
    .fc:focus{outline:none;border-color:#F97316;box-shadow:0 0 0 3px rgba(249,115,22,.08);}
    .err{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;border-radius:7px;padding:10px 13px;font-size:13px;margin-bottom:16px;}
    .btn-login{background:#F97316;border:none;color:#fff;padding:10px 16px;border-radius:7px;font-size:13.5px;font-weight:600;cursor:pointer;width:100%;font-family:inherit;transition:background .1s;display:flex;align-items:center;justify-content:center;gap:7px;margin-top:4px;}
    .btn-login:hover{background:#EA6A0A;}
  </style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-icon"><i class="bi bi-nut-fill"></i></div>
    <div class="brand-name">{{ $appName }}</div>
    <div class="brand-sub">Sistem Manajemen Sparepart</div>
  </div>
  <div class="card">
    @if($errors->any())
      <div class="err"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="fgrp">
        <label class="flbl">Username</label>
        <div class="iw">
          <i class="bi bi-person ic"></i>
          <input type="text" name="username" class="fc" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="username" required>
        </div>
      </div>
      <div class="fgrp" style="margin-bottom:20px;">
        <label class="flbl">Password</label>
        <div class="iw">
          <i class="bi bi-lock ic"></i>
          <input type="password" name="password" class="fc" placeholder="Masukkan password" autocomplete="current-password" required>
        </div>
      </div>
      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Masuk
      </button>
    </form>
  </div>
</div>
</body>
</html>
