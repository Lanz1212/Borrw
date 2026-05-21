<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sparepart MS') — Sparepart Management</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--primary:#1E293B;--primary-l:#334155;--accent:#F97316;--accent-l:#FB923C;--success:#10B981;--warning:#F59E0B;--danger:#EF4444;--info:#3B82F6;--purple:#8B5CF6;--bg:#F1F5F9;--card:#fff;--muted:#64748B;--border:#E2E8F0;--sw:260px;--sm:70px;--th:62px;--r:14px;--shadow:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadowM:0 4px 16px rgba(15,23,42,.08);--shadowL:0 20px 40px rgba(15,23,42,.14);}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{-webkit-text-size-adjust:100%;}
    body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--primary);min-height:100vh;overflow-x:hidden;font-size:14px;}
    h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}
    /* SIDEBAR */
    #shell{display:flex;min-height:100vh;max-width:100vw;overflow-x:hidden;}
    #sb{width:var(--sw);background:linear-gradient(180deg,#1E293B 0%,#0F172A 100%);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:1050;transition:width .28s ease,transform .28s ease;overflow:hidden;}
    #sb.mini{width:var(--sm);}
    .sb-brand{min-height:var(--th);padding:0 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:12px;flex-shrink:0;}
    .sb-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-l));display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;color:#fff;box-shadow:0 4px 12px rgba(249,115,22,.4);}
    .sb-brand-txt{font-family:'Syne',sans-serif;font-weight:700;font-size:13px;line-height:1.3;white-space:nowrap;overflow:hidden;opacity:1;transition:opacity .2s;}
    #sb.mini .sb-brand-txt{opacity:0;pointer-events:none;}
    .sb-nav{flex:1;padding:8px 0;overflow-y:auto;overflow-x:hidden;}
    .sb-nav::-webkit-scrollbar{width:3px;}
    .sb-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.12);border-radius:99px;}
    .nav-sec{font-size:9px;font-weight:700;letter-spacing:2px;color:rgba(255,255,255,.25);padding:16px 18px 4px;white-space:nowrap;overflow:hidden;text-transform:uppercase;transition:opacity .2s;}
    #sb.mini .nav-sec{opacity:0;}
    .nav-btn{display:flex;align-items:center;gap:11px;width:100%;margin:1px 8px;width:calc(100% - 16px);padding:9px 12px;background:none;border:none;color:rgba(255,255,255,.55);cursor:pointer;text-align:left;font-family:'Poppins',sans-serif;font-size:13px;font-weight:400;white-space:nowrap;transition:all .15s;text-decoration:none;border-radius:8px;}
    .nav-btn:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9);}
    .nav-btn.active{background:rgba(249,115,22,.2);color:#FB923C;}
    .nav-ico{font-size:16px;flex-shrink:0;width:20px;text-align:center;}
    .nav-txt{flex:1;overflow:hidden;opacity:1;transition:opacity .2s;}
    #sb.mini .nav-txt{opacity:0;pointer-events:none;}
    .sb-foot{padding:10px;border-top:1px solid rgba(255,255,255,.06);flex-shrink:0;}
    .u-pill{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;background:rgba(255,255,255,.05);overflow:hidden;border:1px solid rgba(255,255,255,.07);}
    .u-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-l));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;color:#fff;}
    .u-inf{overflow:hidden;flex:1;transition:opacity .2s;}
    #sb.mini .u-inf{opacity:0;}
    .u-nm{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#fff;}
    .u-rl{font-size:11px;color:rgba(255,255,255,.4);}
    #sb-ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1040;backdrop-filter:blur(3px);}
    /* TOPBAR */
    #main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh;transition:margin-left .28s ease;width:calc(100% - var(--sw));}
    #main.mini{margin-left:var(--sm);width:calc(100% - var(--sm));}
    #topbar{height:var(--th);background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:12px;position:sticky;top:0;z-index:900;box-shadow:0 1px 0 var(--border),0 2px 8px rgba(15,23,42,.04);flex-shrink:0;}
    #btn-tog{width:36px;height:36px;border:none;background:var(--bg);border-radius:9px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--muted);flex-shrink:0;transition:all .15s;}
    #btn-tog:hover{background:var(--border);color:var(--primary);}
    #pg-title{font-family:'Poppins',sans-serif;font-size:17px;font-weight:600;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .btn-logout{display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1.5px solid var(--border);background:none;font-size:13px;font-weight:500;cursor:pointer;color:var(--muted);transition:all .15s;white-space:nowrap;}
    .btn-logout:hover{background:var(--danger);border-color:var(--danger);color:#fff;}
    /* PAGE & CARDS */
    #pg{flex:1;padding:22px;width:100%;max-width:100%;box-sizing:border-box;overflow-x:hidden;}
    @keyframes pgIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    #pg > *{animation:pgIn .22s ease;}
    .card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow);}
    .p20{padding:20px;}.p16{padding:16px;}
    .stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow);padding:20px;transition:transform .2s,box-shadow .2s;height:100%;display:flex;flex-direction:column;position:relative;overflow:hidden;}
    .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--r) var(--r) 0 0;}
    .sc-orange::before{background:linear-gradient(90deg,var(--accent),var(--accent-l));}
    .sc-green::before{background:linear-gradient(90deg,var(--success),#34D399);}
    .sc-blue::before{background:linear-gradient(90deg,var(--info),#60A5FA);}
    .sc-red::before{background:linear-gradient(90deg,var(--danger),#F87171);}
    .stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadowM);}
    .stat-ico{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:21px;margin-bottom:12px;}
    .si-orange{background:rgba(249,115,22,.1);color:var(--accent);}
    .si-green{background:rgba(16,185,129,.1);color:var(--success);}
    .si-blue{background:rgba(59,130,246,.1);color:var(--info);}
    .si-red{background:rgba(239,68,68,.1);color:var(--danger);}
    .sc-purple::before{background:linear-gradient(90deg,var(--purple),#A78BFA);}
    .si-purple{background:rgba(139,92,246,.1);color:var(--purple);}
    .stat-val{font-family:'Poppins',sans-serif;font-size:30px;font-weight:700;line-height:1;word-break:break-word;}
    .stat-lbl{font-size:12px;color:var(--muted);margin-top:5px;font-weight:500;}
    /* TABLES */
    .tw{border-radius:var(--r);border:1px solid var(--border);overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;background:#fff;display:block;}
    .table{margin:0;width:100%;min-width:650px;border-collapse:separate;border-spacing:0;}
    .table thead th{background:var(--primary);color:#fff;font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;letter-spacing:.4px;border:none;padding:13px 14px;white-space:nowrap;}
    .table tbody td{padding:12px 14px;vertical-align:middle;border-bottom:1px solid var(--border);font-size:13px;white-space:nowrap;}
    .table tbody tr:last-child td{border-bottom:none;}
    .table tbody tr:hover td{background:rgba(15,23,42,.025);}
    .tr-click{cursor:pointer;}
    .tr-click:hover td{background:rgba(15,23,42,.04)!important;}
    /* BADGES */
    .bdg{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;}
    .b-pinjam{background:rgba(59,130,246,.12);color:#1D4ED8;}
    .b-consumable{background:rgba(245,158,11,.12);color:#92400E;}
    .b-aktif{background:rgba(16,185,129,.12);color:#065F46;}
    .b-selesai{background:rgba(100,116,139,.1);color:var(--muted);}
    .b-dipakai,.b-diambil{background:rgba(100,116,139,.1);color:var(--muted);}
    .b-partial{background:rgba(249,115,22,.12);color:#9A3412;}
    .b-dipinjam{background:rgba(59,130,246,.12);color:#1D4ED8;}
    .b-kembali{background:rgba(16,185,129,.12);color:#065F46;}
    .b-baik{background:rgba(16,185,129,.12);color:#065F46;}
    .b-rusak{background:rgba(239,68,68,.12);color:#991B1B;}
    .b-perlu_perbaikan{background:rgba(245,158,11,.12);color:#92400E;}
    .b-admin{background:rgba(139,92,246,.12);color:#5B21B6;}
    .b-user{background:rgba(100,116,139,.1);color:var(--muted);}
    .b-cat{background:rgba(100,116,139,.1);color:var(--muted);}
    .low-pill{display:inline-flex;align-items:center;gap:3px;background:rgba(245,158,11,.12);color:#92400E;font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;}
    /* BUTTONS */
    .b-pri{background:var(--primary);border:none;color:#fff;padding:9px 18px;border-radius:9px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 2px 8px rgba(30,41,59,.2);}
    .b-pri:hover{background:var(--primary-l);transform:translateY(-1px);box-shadow:0 4px 14px rgba(30,41,59,.3);}
    .b-acc{background:linear-gradient(135deg,var(--accent),#EA6A0A);border:none;color:#fff;padding:9px 18px;border-radius:9px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 2px 8px rgba(249,115,22,.3);}
    .b-acc:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(249,115,22,.4);}
    .b-out{background:#fff;border:1.5px solid var(--border);color:var(--primary);padding:8px 16px;border-radius:9px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:6px;}
    .b-out:hover{border-color:var(--primary-l);background:var(--bg);}
    .b-xl{background:rgba(22,163,74,.08);border:1.5px solid rgba(22,163,74,.3);color:#16A34A;padding:8px 16px;border-radius:9px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:6px;}
    .b-xl:hover{background:rgba(22,163,74,.15);border-color:rgba(22,163,74,.5);}
    .w-100{width:100%;}
    /* FORMS */
    .fc,.fs{border:1.5px solid var(--border);border-radius:9px;padding:10px 13px;font-size:13.5px;font-family:'Poppins',sans-serif;width:100%;max-width:100%;transition:border-color .15s,box-shadow .15s;background:#fff;color:var(--primary);box-sizing:border-box;}
    .fc:focus,.fs:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(249,115,22,.1);}
    .fc-sm{padding:7px 10px;font-size:12.5px;border-radius:8px;}
    textarea.fc{resize:vertical;min-height:80px;}
    .flbl{font-weight:600;font-size:12.5px;margin-bottom:5px;display:block;color:var(--primary);}
    .fgrp{margin-bottom:14px;width:100%;}
    /* AUTO-SUGGEST */
    .sg-wrap{position:relative;flex:1;width:100%;}
    .sg-drop{position:absolute;z-index:1300;background:#fff;border:1.5px solid var(--border);border-radius:10px;box-shadow:var(--shadowL);max-height:220px;overflow-y:auto;width:100%;top:calc(100% + 4px);left:0;display:none;box-sizing:border-box;}
    .sg-item{padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border);transition:background .1s;}
    .sg-item:last-child{border-bottom:none;}
    .sg-item:hover{background:var(--bg);}
    .sg-sel{display:flex;align-items:center;gap:8px;padding:8px 12px;background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);border-radius:8px;font-size:13px;margin-top:6px;word-break:break-word;width:100%;}
    .sg-sel-x{margin-left:auto;background:none;border:none;cursor:pointer;color:var(--muted);padding:2px 4px;border-radius:4px;display:flex;flex-shrink:0;}
    .sg-sel-x:hover{color:var(--danger);}
    /* MODAL */
    .modal-content{border-radius:var(--r);border:none;box-shadow:var(--shadowL);}
    .modal-header{background:var(--primary);color:#fff;border-radius:var(--r) var(--r) 0 0;padding:14px 20px;border:none;}
    .modal-header .btn-close{filter:brightness(0) invert(1);}
    .modal-title{font-family:'Poppins',sans-serif;font-weight:600;font-size:15px;display:flex;align-items:center;gap:8px;}
    .modal-body{padding:20px;max-width:100vw;overflow-x:hidden;}
    .modal-footer{padding:14px 20px;border-top:1px solid var(--border);gap:8px;flex-wrap:wrap;}
    /* DETAIL GRID */
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px;margin-bottom:16px;}
    .dg-full{grid-column:1/-1;}
    .dg-lbl{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--muted);margin-bottom:3px;}
    .dg-val{font-size:14px;font-weight:500;word-wrap:break-word;word-break:break-word;white-space:normal;}
    /* CART ITEM */
    .cart-item{display:flex;align-items:center;gap:10px;padding:12px;background:var(--bg);border-radius:9px;margin-bottom:8px;border:1px solid var(--border);flex-wrap:wrap;}
    .c-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
    .c-info{flex:1;min-width:120px;}
    .c-ctrl{display:flex;align-items:center;gap:5px;flex-shrink:0;}
    .c-btn{width:28px;height:28px;border:1.5px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--primary);transition:all .15s;flex-shrink:0;}
    .c-btn:hover{border-color:var(--accent);color:var(--accent);}
    .c-num{font-weight:700;min-width:24px;text-align:center;font-size:14px;}
    /* SIGNATURE */
    #sig-cv{border:2px dashed var(--border);border-radius:10px;cursor:crosshair;display:block;width:100%;max-width:100%;touch-action:none;background:#fff;box-sizing:border-box;}
    /* EMPTY STATE */
    .empty{text-align:center;padding:36px 16px;color:var(--muted);width:100%;}
    .empty .ei{font-size:38px;opacity:.35;margin-bottom:8px;}
    .empty p{font-size:13px;margin:0;}
    /* LOADING */
    #ld-ov{position:fixed;inset:0;background:rgba(15,23,42,.5);display:none;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(4px);}
    .ld-box{background:#fff;border-radius:16px;padding:28px 40px;text-align:center;box-shadow:var(--shadowL);}
    .ld-box .spinner-border{color:var(--accent);width:2.5rem;height:2.5rem;}
    .ld-box p{font-size:13px;margin-top:12px;color:var(--muted);font-weight:500;}
    /* SEARCH */
    .sw{position:relative;width:100%;}
    .sw .bi-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none;}
    .sw input{padding-left:36px;}
    /* TOAST */
    #tc{position:fixed;top:14px;right:14px;z-index:9998;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
    .ti{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:12px;font-size:13px;font-weight:500;min-width:260px;max-width:calc(100vw - 28px);box-shadow:0 8px 24px rgba(15,23,42,.15);cursor:pointer;animation:tIn .25s cubic-bezier(.16,1,.3,1);pointer-events:auto;background:#fff;border:1px solid var(--border);}
    @keyframes tIn{from{opacity:0;transform:translateX(14px) scale(.98);}to{opacity:1;transform:translateX(0) scale(1);}}
    .ti-ico{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;margin-top:1px;}
    .t-s .ti-ico{background:rgba(16,185,129,.1);color:var(--success);}
    .t-d .ti-ico{background:rgba(239,68,68,.1);color:var(--danger);}
    .t-w .ti-ico{background:rgba(245,158,11,.1);color:var(--warning);}
    .t-i .ti-ico{background:rgba(59,130,246,.1);color:var(--info);}
    .ti-msg{flex:1;word-break:break-word;line-height:1.45;}
    /* SECTION HEADER */
    .sh{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;width:100%;}
    .st{font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;display:flex;align-items:center;gap:8px;}
    /* RESPONSIVE */
    @media(max-width:767px){
      #sb{transform:translateX(-100%);width:var(--sw)!important;}
      #sb.mob-open{transform:translateX(0);}
      #main{margin-left:0!important;width:100%!important;}
      #pg{padding:12px;}
      #topbar{padding:0 12px;gap:8px;}
      #pg-title{font-size:15px;}
      .sh{flex-direction:column;align-items:stretch;gap:8px;}
      .sh>button,.sh>a{width:100%;justify-content:center;}
      .stat-val{font-size:22px;}
      .stat-card{padding:14px 12px;}
      .stat-ico{width:36px;height:36px;font-size:16px;margin-bottom:8px;}
      .stat-lbl{font-size:11px;}
      .btn-logout span{display:none;}.btn-logout{padding:7px 10px;}
      .fgrp,.fc,.fs,.sg-wrap{max-width:100%;box-sizing:border-box;}
      .detail-grid{grid-template-columns:1fr;gap:10px;}
      .cart-item{flex-direction:column;align-items:flex-start;}
      .c-info{width:100%;margin-bottom:6px;}
      .c-ctrl{width:100%;justify-content:flex-end;padding-top:8px;border-top:1px dashed var(--border);}
      .modal-body{padding:14px;}
      .modal-footer{justify-content:stretch;}
      .modal-footer .b-out,.modal-footer .b-acc{flex:1;}
      .table{min-width:480px;}
      .table thead th{padding:10px 10px;font-size:10.5px;}
      .table tbody td{padding:10px 10px;font-size:12.5px;}
      .bdg{font-size:10.5px;padding:3px 8px;}
      .card.p20{padding:14px;}
      .card.p16{padding:12px;}
    }
    @media(min-width:768px) and (max-width:1024px){
      #pg{padding:16px;}
      .stat-val{font-size:26px;}
      .stat-card{padding:16px 14px;}
      .table thead th{font-size:11px;}
      .table tbody td{font-size:13px;}
    }
    @media(min-width:768px){
      #sb-ov{display:none!important;}
      .sw{width:220px;}
    }
  </style>
  @stack('styles')
</head>
<body>
<!-- LOADING OVERLAY -->
<div id="ld-ov"><div class="ld-box"><div class="spinner-border" role="status"></div><p>Memuat data...</p></div></div>
<!-- TOAST CONTAINER -->
<div id="tc"></div>
<!-- SIDEBAR OVERLAY -->
<div id="sb-ov" onclick="closeSb()"></div>

<div id="shell">
  <nav id="sb">
    <div class="sb-brand">
      <div class="sb-icon"><i class="bi bi-gear-fill"></i></div>
      <div class="sb-brand-txt">
        <div id="app-nm" style="font-size:14px;">{{ \App\Models\Setting::get('app_name','Sparepart MS') }}</div>
        <div style="font-size:10px;opacity:.6;font-weight:400;">Management System</div>
      </div>
    </div>
    <div class="sb-nav">
      <div class="nav-sec">UTAMA</div>
      <a href="{{ route('dashboard') }}" class="nav-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 nav-ico"></i><span class="nav-txt">Dashboard</span>
      </a>
      <div class="nav-sec">INVENTORI</div>
      <a href="{{ route('inventory.index') }}" class="nav-btn {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam nav-ico"></i><span class="nav-txt">Inventori Barang</span>
      </a>
      <a href="{{ route('borrowers.index') }}" class="nav-btn {{ request()->routeIs('borrowers.*') ? 'active' : '' }}">
        <i class="bi bi-people nav-ico"></i><span class="nav-txt">Data Peminjam</span>
      </a>
      <div class="nav-sec">TRANSAKSI</div>
      <a href="{{ route('transactions.index') }}" class="nav-btn {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right nav-ico"></i><span class="nav-txt">Pinjam / Ambil</span>
      </a>
      <a href="{{ route('transactions.history') }}" class="nav-btn {{ request()->routeIs('transactions.history') ? 'active' : '' }}">
        <i class="bi bi-clock-history nav-ico"></i><span class="nav-txt">Riwayat Transaksi</span>
      </a>
      <a href="{{ route('returns.index') }}" class="nav-btn {{ request()->routeIs('returns.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-return-left nav-ico"></i><span class="nav-txt">Pengembalian</span>
      </a>
      <a href="{{ route('damaged.index') }}" class="nav-btn {{ request()->routeIs('damaged.index') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle nav-ico"></i><span class="nav-txt">Catat Rusak</span>
      </a>
      <a href="{{ route('damaged.history') }}" class="nav-btn {{ request()->routeIs('damaged.history') ? 'active' : '' }}">
        <i class="bi bi-clipboard-x nav-ico"></i><span class="nav-txt">Riwayat Rusak</span>
      </a>
      @if(auth()->user()->isAdmin())
      <div class="nav-sec">ADMINISTRASI</div>
      <a href="{{ route('users.index') }}" class="nav-btn {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-person-gear nav-ico"></i><span class="nav-txt">Manajemen User</span>
      </a>
      <a href="{{ route('settings.index') }}" class="nav-btn {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="bi bi-gear nav-ico"></i><span class="nav-txt">Pengaturan</span>
      </a>
      @endif
    </div>
    <div class="sb-foot">
      <div class="u-pill">
        <div class="u-av">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="u-inf">
          <div class="u-nm">{{ auth()->user()->name }}</div>
          <div class="u-rl">
            @if(auth()->user()->isAdmin())
              <i class="bi bi-shield-lock-fill"></i> Admin
            @else
              <i class="bi bi-person-fill"></i> User
            @endif
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div id="main">
    <header id="topbar">
      <button id="btn-tog"><i class="bi bi-list"></i></button>
      <div id="pg-title">@yield('page-title', 'Dashboard')</div>
      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="btn-logout"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
      </form>
    </header>
    <main id="pg">
      @yield('content')
    </main>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const IS_ADMIN = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};

function ld(v){document.getElementById('ld-ov').style.display=v?'flex':'none';}

function toast(msg,t='success'){
  const ico={success:'check-circle-fill',danger:'x-circle-fill',warning:'exclamation-triangle-fill',info:'info-circle-fill'};
  const cl={success:'t-s',danger:'t-d',warning:'t-w',info:'t-i'};
  const el=document.createElement('div');
  el.className='ti '+(cl[t]||'t-s');
  el.innerHTML=`<div class="ti-ico"><i class="bi bi-${ico[t]||'info-circle-fill'}"></i></div><span class="ti-msg">${esc(msg)}</span>`;
  el.onclick=()=>el.remove();
  document.getElementById('tc').appendChild(el);
  setTimeout(()=>{if(el.parentNode)el.remove();},4500);
}

async function api(url,method='GET',data=null){
  const opts={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}};
  if(data){opts.headers['Content-Type']='application/json';opts.body=JSON.stringify(data);}
  try{
    const r=await fetch(url,opts);
    const json=await r.json();
    if(!r.ok && !json.success){throw new Error(json.message||(json.errors?Object.values(json.errors)[0][0]:'Server error'));}
    return json;
  } catch(e){
    if(e.name==='SyntaxError')return{success:false,message:'Server error'};
    throw e;
  }
}

function esc(v){if(v==null)return'';return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function fd(s){if(!s)return'—';try{return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}catch(e){return s;}}
function fdt(s){if(!s)return'—';try{return new Date(s).toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return s;}}
function nowLocal(){const d=new Date();return new Date(d-d.getTimezoneOffset()*60000).toISOString().slice(0,16);}

function statusLabel(s){
  return{aktif:'Aktif',selesai:'Selesai',partial:'Sebagian Kembali',dipinjam:'Dipinjam',kembali:'Kembali',dipakai:'Dipakai',diambil:'Diambil',baik:'Baik',rusak:'Rusak',perlu_perbaikan:'Perlu Perbaikan',admin:'Admin',user:'User'}[s]||s;
}

function openSb(){document.getElementById('sb').classList.add('mob-open');document.getElementById('sb-ov').style.display='block';}
function closeSb(){document.getElementById('sb').classList.remove('mob-open');document.getElementById('sb-ov').style.display='none';}

document.getElementById('btn-tog').addEventListener('click',()=>{
  if(window.innerWidth<768){
    document.getElementById('sb').classList.contains('mob-open')?closeSb():openSb();
  } else {
    document.getElementById('sb').classList.toggle('mini');
    document.getElementById('main').classList.toggle('mini');
  }
});
if(window.innerWidth>=768&&window.innerWidth<=1024){
  document.getElementById('sb').classList.add('mini');
  document.getElementById('main').classList.add('mini');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
function exportXlsx(rows,filename){
  if(typeof XLSX==='undefined'){toast('Library XLSX belum dimuat, coba refresh halaman.','warning');return;}
  const ws=XLSX.utils.aoa_to_sheet(rows);
  const wb=XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb,ws,'Data');
  XLSX.writeFile(wb,filename);
}
function dateStr(){const d=new Date();return d.getFullYear()+String(d.getMonth()+1).padStart(2,'0')+String(d.getDate()).padStart(2,'0');}
function renderSig(src,el){
  if(!el)return;
  const img=new Image();
  img.onload=function(){
    const cv=document.createElement('canvas');
    cv.width=img.width;cv.height=img.height;
    cv.style.cssText='max-width:260px;max-height:110px;width:auto;height:auto;display:block;border-radius:4px;';
    const ctx=cv.getContext('2d');
    ctx.drawImage(img,0,0);
    const d=ctx.getImageData(0,0,cv.width,cv.height);
    const bgLum=0.2126*d.data[0]+0.7152*d.data[1]+0.0722*d.data[2];
    const darkBg=bgLum<50;
    for(let i=0;i<d.data.length;i+=4){
      const lum=0.2126*d.data[i]+0.7152*d.data[i+1]+0.0722*d.data[i+2];
      d.data[i]=d.data[i+1]=d.data[i+2]=darkBg?(lum<30?255:0):(lum>200?255:0);
      d.data[i+3]=255;
    }
    ctx.putImageData(d,0,0);
    el.innerHTML='';el.appendChild(cv);
  };
  img.onerror=function(){el.innerHTML=`<img src="${src}" style="max-width:260px;max-height:110px;display:block;">`;};
  img.src=src;
}
</script>
@stack('scripts')
</body>
</html>
