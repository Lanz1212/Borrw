<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sparepart MS') — Sparepart Management</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--primary:#0F172A;--accent:#F97316;--accent-d:#EA6A0A;--success:#059669;--warning:#D97706;--danger:#DC2626;--info:#2563EB;--purple:#7C3AED;--bg:#F8FAFC;--card:#fff;--muted:#64748B;--border:#E2E8F0;--sw:244px;--sm:58px;--th:56px;--r:8px;--shadow:0 1px 2px rgba(0,0,0,.05);--shadowM:0 4px 16px rgba(0,0,0,.08);--shadowL:0 16px 48px rgba(0,0,0,.12);--nav-active-bg:rgba(249,115,22,.08);--nav-active-txt:#FB923C;--focus-ring:rgba(249,115,22,.08);}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{-webkit-text-size-adjust:100%;}
    body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--primary);min-height:100vh;overflow-x:hidden;font-size:14px;-webkit-font-smoothing:antialiased;}
    h1,h2,h3,h4,h5,h6{font-family:'Inter',system-ui,-apple-system,sans-serif;font-weight:600;}
    /* SIDEBAR */
    #shell{display:flex;min-height:100vh;}
    #sb{width:var(--sw);background:#0D1117;border-right:1px solid rgba(255,255,255,.04);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:1050;transition:width .22s ease,transform .22s ease;overflow:hidden;}
    #sb.mini{width:var(--sm);}
    .sb-brand{min-height:var(--th);padding:0 14px;border-bottom:1px solid rgba(255,255,255,.04);display:flex;align-items:center;gap:10px;flex-shrink:0;}
    .sb-icon{width:30px;height:30px;border-radius:7px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;color:#fff;}
    .sb-brand-txt{overflow:hidden;opacity:1;transition:opacity .18s;min-width:0;}
    .sb-brand-nm{color:#fff;font-size:13.5px;font-weight:600;white-space:nowrap;letter-spacing:-.01em;}
    .sb-brand-sub{color:rgba(255,255,255,.28);font-size:10px;font-weight:400;white-space:nowrap;}
    #sb.mini .sb-brand-txt{opacity:0;pointer-events:none;}
    .sb-nav{flex:1;padding:6px 0;overflow-y:auto;overflow-x:hidden;}
    .sb-nav::-webkit-scrollbar{width:0;}
    .nav-sec{font-size:9px;font-weight:700;letter-spacing:1.5px;color:rgba(255,255,255,.18);padding:14px 18px 4px;white-space:nowrap;text-transform:uppercase;transition:opacity .18s;}
    #sb.mini .nav-sec{opacity:0;}
    .nav-btn{display:flex;align-items:center;gap:10px;width:calc(100% - 14px);margin:1px 7px;padding:8px 9px;background:none;border:none;border-left:2px solid transparent;color:rgba(255,255,255,.4);cursor:pointer;text-align:left;font-family:inherit;font-size:13px;font-weight:400;white-space:nowrap;transition:color .12s,background .12s,border-color .12s;text-decoration:none;border-radius:6px;}
    .nav-btn:hover{background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);}
    .nav-btn.active{background:var(--nav-active-bg);color:var(--nav-active-txt);border-left-color:var(--accent);font-weight:500;}
    .nav-ico{font-size:14px;flex-shrink:0;width:18px;text-align:center;}
    .nav-txt{flex:1;overflow:hidden;opacity:1;transition:opacity .18s;}
    #sb.mini .nav-txt{opacity:0;pointer-events:none;}
    .sb-foot{padding:10px;border-top:1px solid rgba(255,255,255,.04);flex-shrink:0;}
    .u-pill{display:flex;align-items:center;gap:9px;padding:8px 9px;border-radius:7px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.05);overflow:hidden;}
    .u-av{width:28px;height:28px;border-radius:6px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;flex-shrink:0;color:#fff;}
    .u-inf{overflow:hidden;flex:1;transition:opacity .18s;}
    #sb.mini .u-inf{opacity:0;}
    .u-nm{font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#fff;}
    .u-rl{font-size:10px;color:rgba(255,255,255,.3);}
    #sb-ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1040;}
    /* TOPBAR */
    #main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh;transition:margin-left .22s ease;width:calc(100% - var(--sw));}
    #main.mini{margin-left:var(--sm);width:calc(100% - var(--sm));}
    #topbar{height:var(--th);background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:12px;position:sticky;top:0;z-index:900;flex-shrink:0;}
    #btn-tog{width:30px;height:30px;border:1px solid var(--border);background:#fff;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--muted);flex-shrink:0;transition:all .1s;}
    #btn-tog:hover{background:var(--bg);color:var(--primary);}
    #pg-title{font-size:15px;font-weight:600;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;letter-spacing:-.02em;}
    .btn-logout{display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:6px;border:1px solid var(--border);background:#fff;font-size:12.5px;font-weight:500;cursor:pointer;color:var(--muted);font-family:inherit;transition:all .1s;white-space:nowrap;}
    .btn-logout:hover{background:#FEF2F2;border-color:#FECACA;color:var(--danger);}
    /* PAGE */
    #pg{flex:1;padding:20px;width:100%;box-sizing:border-box;overflow-x:hidden;}
    @keyframes pgIn{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}
    #pg>*{animation:pgIn .18s ease;}
    .card{background:#fff;border:1px solid var(--border);border-radius:var(--r);box-shadow:none;}
    .p20{padding:20px;}.p16{padding:16px;}
    /* STAT CARDS — clean, flat, no colored strips */
    .stat-card{background:#fff;border:1px solid var(--border);border-radius:var(--r);padding:18px 20px;transition:border-color .15s;height:100%;display:flex;flex-direction:column;}
    .stat-card:hover{border-color:#CBD5E1;}
    .sc-orange::before,.sc-green::before,.sc-blue::before,.sc-red::before,.sc-purple::before{display:none;}
    .stat-ico{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:12px;}
    .si-orange{background:#FFF7ED;color:#C2410C;}
    .si-green{background:#F0FDF4;color:#15803D;}
    .si-blue{background:#EFF6FF;color:#1D4ED8;}
    .si-red{background:#FEF2F2;color:#B91C1C;}
    .si-purple{background:#F5F3FF;color:#7C3AED;}
    .stat-val{font-size:28px;font-weight:700;line-height:1;letter-spacing:-.03em;}
    .stat-lbl{font-size:12px;color:var(--muted);margin-top:4px;font-weight:400;}
    /* TABLES — light header, clean rows */
    .tw{border-radius:var(--r);border:1px solid var(--border);overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;background:#fff;display:block;}
    .table{margin:0;width:100%;min-width:600px;border-collapse:separate;border-spacing:0;}
    .table thead th{background:#F8FAFC;color:#374151;font-family:inherit;font-size:11px;font-weight:600;letter-spacing:.5px;border-bottom:1px solid var(--border);padding:11px 14px;white-space:nowrap;text-transform:uppercase;}
    .table tbody td{padding:11px 14px;vertical-align:middle;border-bottom:1px solid var(--border);font-size:13px;white-space:nowrap;}
    .table tbody tr:last-child td{border-bottom:none;}
    .table tbody tr:hover td{background:#F8FAFC;}
    .tr-click{cursor:pointer;}
    .tr-click:hover td{background:#F8FAFC!important;}
    /* BADGES — subtle, rectangular */
    .bdg{display:inline-flex;align-items:center;padding:3px 7px;border-radius:4px;font-size:11px;font-weight:500;white-space:nowrap;}
    .b-pinjam{background:#EFF6FF;color:#1D4ED8;}
    .b-consumable{background:#FFFBEB;color:#92400E;}
    .b-bon{background:#FFF7ED;color:#C2410C;}
    .b-aktif{background:#F0FDF4;color:#15803D;}
    .b-selesai{background:#F1F5F9;color:#475569;}
    .b-dipakai,.b-diambil{background:#F1F5F9;color:#475569;}
    .b-partial{background:#FFF7ED;color:#C2410C;}
    .b-menunggu_persetujuan{background:#FFFBEB;color:#B45309;}
    .b-ditolak{background:#FEF2F2;color:#B91C1C;}
    .b-dipinjam{background:#EFF6FF;color:#1D4ED8;}
    .b-kembali{background:#F0FDF4;color:#15803D;}
    .b-baik{background:#F0FDF4;color:#15803D;}
    .b-rusak{background:#FEF2F2;color:#B91C1C;}
    .b-perlu_perbaikan{background:#FFFBEB;color:#B45309;}
    .b-admin{background:#F5F3FF;color:#7C3AED;}
    .b-user{background:#F1F5F9;color:#475569;}
    .b-cat{background:#F1F5F9;color:#475569;}
    .low-pill{display:inline-flex;align-items:center;gap:3px;background:#FFF7ED;color:#C2410C;font-size:10.5px;font-weight:500;padding:2px 7px;border-radius:4px;}
    /* BUTTONS — flat, no gradients */
    .b-pri{background:var(--primary);border:none;color:#fff;padding:8px 15px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;transition:opacity .1s;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-family:inherit;}
    .b-pri:hover{opacity:.88;}
    .b-acc{background:var(--accent);border:none;color:#fff;padding:8px 15px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;transition:background .1s;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-family:inherit;}
    .b-acc:hover{background:var(--accent-d);}
    .b-out{background:#fff;border:1px solid var(--border);color:var(--primary);padding:8px 15px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;transition:border-color .1s,background .1s;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-family:inherit;}
    .b-out:hover{background:var(--bg);border-color:#CBD5E1;}
    .b-xl{background:#F0FDF4;border:1px solid #BBF7D0;color:#15803D;padding:8px 15px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;transition:background .1s;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-family:inherit;}
    .b-xl:hover{background:#DCFCE7;}
    .w-100{width:100%;}
    /* FORMS */
    .fc,.fs{border:1px solid var(--border);border-radius:6px;padding:9px 12px;font-size:13.5px;font-family:inherit;width:100%;max-width:100%;transition:border-color .12s,box-shadow .12s;background:#fff;color:var(--primary);box-sizing:border-box;}
    .fc:focus,.fs:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--focus-ring);}
    .fc-sm{padding:6px 10px;font-size:12px;}
    textarea.fc{resize:vertical;min-height:80px;}
    .flbl{font-weight:500;font-size:12.5px;margin-bottom:5px;display:block;color:#374151;}
    .fgrp{margin-bottom:14px;width:100%;}
    /* AUTO-SUGGEST */
    .sg-wrap{position:relative;flex:1;width:100%;}
    .sg-drop{position:absolute;z-index:1300;background:#fff;border:1px solid var(--border);border-radius:8px;box-shadow:var(--shadowL);max-height:220px;overflow-y:auto;width:100%;top:calc(100% + 4px);left:0;display:none;box-sizing:border-box;}
    .sg-item{padding:9px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border);transition:background .08s;}
    .sg-item:last-child{border-bottom:none;}
    .sg-item:hover{background:#F8FAFC;}
    .sg-sel{display:flex;align-items:center;gap:8px;padding:8px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:6px;font-size:13px;margin-top:6px;word-break:break-word;width:100%;}
    .sg-sel-x{margin-left:auto;background:none;border:none;cursor:pointer;color:var(--muted);padding:2px 4px;display:flex;flex-shrink:0;}
    /* PHOTO WIDGET */
    .pw-zone{border:2px dashed var(--border);border-radius:10px;padding:18px 14px;text-align:center;background:#FAFAFA;cursor:pointer;transition:border-color .15s,background .15s;position:relative;}
    .pw-zone:hover{border-color:var(--accent);background:#FFF7ED;}
    .pw-zone.has-photo{border-style:solid;border-color:#BBF7D0;background:#F0FDF4;padding:10px;}
    .pw-preview{display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;}
    .pw-img{width:88px;height:72px;object-fit:cover;border-radius:7px;border:1.5px solid var(--border);cursor:pointer;flex-shrink:0;}
    .pw-btns{display:flex;flex-direction:column;gap:6px;flex:1;min-width:80px;}
    .pw-btn-cam{background:var(--primary);color:#fff;border:none;border-radius:6px;padding:6px 10px;font-size:11.5px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:5px;width:100%;}
    .pw-btn-file{background:#fff;color:var(--primary);border:1px solid var(--border);border-radius:6px;padding:6px 10px;font-size:11.5px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:5px;width:100%;}
    .pw-btn-del{background:#FEF2F2;color:var(--danger);border:1px solid #FECACA;border-radius:6px;padding:6px 10px;font-size:11.5px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:5px;width:100%;}
    .pw-error{font-size:11.5px;color:var(--danger);margin-top:6px;display:none;}
    .pw-req-badge{display:inline-block;background:#FEF2F2;color:var(--danger);border-radius:3px;font-size:10px;font-weight:600;padding:1px 5px;margin-left:4px;}
    .sg-sel-x:hover{color:var(--danger);}
    /* MODAL — light header */
    .modal-content{border-radius:10px;border:1px solid var(--border);box-shadow:var(--shadowL);}
    .modal-header{background:#fff;color:var(--primary);border-radius:10px 10px 0 0;padding:15px 20px;border-bottom:1px solid var(--border);}
    .modal-header .btn-close{filter:none;}
    .modal-title{font-family:inherit;font-weight:600;font-size:14px;display:flex;align-items:center;gap:8px;}
    .modal-body{padding:20px;max-width:100vw;overflow-x:hidden;}
    .modal-footer{padding:12px 20px;border-top:1px solid var(--border);gap:8px;flex-wrap:wrap;}
    /* DETAIL GRID */
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;margin-bottom:16px;}
    .dg-full{grid-column:1/-1;}
    .dg-lbl{font-size:10.5px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--muted);margin-bottom:2px;}
    .dg-val{font-size:13.5px;font-weight:500;word-wrap:break-word;word-break:break-word;}
    /* CART */
    .cart-item{display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg);border-radius:6px;margin-bottom:6px;border:1px solid var(--border);flex-wrap:wrap;}
    .c-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
    .c-info{flex:1;min-width:120px;}
    .c-ctrl{display:flex;align-items:center;gap:4px;flex-shrink:0;}
    .c-btn{width:26px;height:26px;border:1px solid var(--border);border-radius:5px;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--primary);transition:all .1s;flex-shrink:0;}
    .c-btn:hover{border-color:var(--accent);color:var(--accent);}
    .c-num{font-weight:600;min-width:22px;text-align:center;font-size:13px;}
    /* SIGNATURE */
    #sig-cv{display:block;width:100%;max-width:100%;touch-action:none;background:#fff;box-sizing:border-box;}
    /* EMPTY */
    .empty{text-align:center;padding:32px 16px;color:var(--muted);width:100%;}
    .empty .ei{font-size:30px;opacity:.25;margin-bottom:8px;}
    .empty p{font-size:13px;margin:0;}
    /* LOADING */
    #ld-ov{position:fixed;inset:0;background:rgba(0,0,0,.3);display:none;align-items:center;justify-content:center;z-index:9999;}
    .ld-box{background:#fff;border-radius:10px;padding:24px 36px;text-align:center;border:1px solid var(--border);}
    .ld-box .spinner-border{color:var(--accent);width:1.8rem;height:1.8rem;}
    .ld-box p{font-size:12.5px;margin-top:10px;color:var(--muted);}
    /* SEARCH */
    .sw{position:relative;width:100%;}
    .sw .bi-search{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;pointer-events:none;}
    .sw input{padding-left:33px;}
    /* TOAST */
    #tc{position:fixed;top:14px;right:14px;z-index:9998;display:flex;flex-direction:column;gap:6px;pointer-events:none;}
    .ti{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:8px;font-size:13px;font-weight:500;min-width:256px;max-width:calc(100vw - 28px);box-shadow:0 4px 20px rgba(0,0,0,.1);cursor:pointer;animation:tIn .18s ease;pointer-events:auto;background:#fff;border:1px solid var(--border);}
    @keyframes tIn{from{opacity:0;transform:translateX(10px);}to{opacity:1;transform:translateX(0);}}
    .ti-ico{width:26px;height:26px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;}
    .t-s .ti-ico{background:#F0FDF4;color:#15803D;}
    .t-d .ti-ico{background:#FEF2F2;color:#B91C1C;}
    .t-w .ti-ico{background:#FFFBEB;color:#B45309;}
    .t-i .ti-ico{background:#EFF6FF;color:#1D4ED8;}
    .ti-msg{flex:1;word-break:break-word;line-height:1.4;}
    /* SECTION HEADER */
    .sh{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:18px;width:100%;}
    .st{font-family:inherit;font-size:14px;font-weight:600;display:flex;align-items:center;gap:7px;letter-spacing:-.01em;}
    /* RESPONSIVE */
    @media(max-width:767px){
      #sb{transform:translateX(-100%);width:var(--sw)!important;}
      #sb.mob-open{transform:translateX(0);}
      #main{margin-left:0!important;width:100%!important;}
      #pg{padding:12px;}
      #topbar{padding:0 12px;gap:8px;}
      #pg-title{font-size:14px;}
      .sh{flex-direction:column;align-items:stretch;gap:8px;}
      .sh>button,.sh>a{width:100%;justify-content:center;}
      .stat-val{font-size:23px;}
      .stat-card{padding:14px 16px;}
      .stat-ico{width:34px;height:34px;font-size:15px;margin-bottom:8px;}
      .btn-logout span{display:none;}.btn-logout{padding:6px 9px;}
      .detail-grid{grid-template-columns:1fr;gap:10px;}
      .cart-item{flex-direction:column;align-items:flex-start;}
      .c-info{width:100%;margin-bottom:4px;}
      .c-ctrl{width:100%;justify-content:flex-end;padding-top:6px;border-top:1px solid var(--border);}
      .modal-body{padding:14px;}
      .modal-footer{justify-content:stretch;}
      .modal-footer .b-out,.modal-footer .b-acc{flex:1;}
      .table{min-width:480px;}
      .table thead th{padding:9px 10px;font-size:10px;}
      .table tbody td{padding:9px 10px;font-size:12.5px;}
      .card.p20{padding:14px;}
    }
    @media(min-width:768px) and (max-width:1024px){
      #pg{padding:16px;}
      .stat-val{font-size:24px;}
    }
    @media(min-width:768px){
      #sb-ov{display:none!important;}
      .sw{width:220px;}
    }
    /* DARK MODE */
    [data-dark="1"]{--bg:#0F172A;--card:#1E293B;--border:#2D3748;--primary:#F1F5F9;--muted:#94A3B8;}
    [data-dark="1"] body{background:var(--bg);}
    [data-dark="1"] .card,[data-dark="1"] .stat-card{background:var(--card);border-color:var(--border);}
    [data-dark="1"] .tw{background:var(--card);border-color:var(--border);}
    [data-dark="1"] .table thead th{background:#263148;color:#CBD5E1;border-bottom-color:var(--border);}
    [data-dark="1"] .table tbody td{border-bottom-color:var(--border);color:var(--primary);}
    [data-dark="1"] .table tbody tr:hover td,[data-dark="1"] .tr-click:hover td{background:#263148!important;}
    [data-dark="1"] .fc,[data-dark="1"] .fs,[data-dark="1"] textarea.fc{background:#263148;color:var(--primary);border-color:var(--border);}
    [data-dark="1"] .fc::placeholder,[data-dark="1"] .fs::placeholder,[data-dark="1"] textarea.fc::placeholder{color:#64748B;}
    [data-dark="1"] #topbar{background:var(--card);border-bottom-color:var(--border);}
    [data-dark="1"] #pg-title{color:var(--primary);}
    [data-dark="1"] .b-out{background:var(--card);border-color:var(--border);color:var(--primary);}
    [data-dark="1"] .b-out:hover{background:#263148;}
    [data-dark="1"] .modal-content,[data-dark="1"] .modal-header,[data-dark="1"] .modal-footer{background:var(--card);border-color:var(--border);color:var(--primary);}
    [data-dark="1"] .modal-header .btn-close{filter:invert(1);}
    [data-dark="1"] .modal-title,[data-dark="1"] .dg-val{color:var(--primary);}
    [data-dark="1"] .sg-drop{background:var(--card);border-color:var(--border);}
    [data-dark="1"] .sg-item{border-bottom-color:var(--border);color:var(--primary);}
    [data-dark="1"] .sg-item:hover{background:#263148;}
    [data-dark="1"] .cart-item{background:#263148;border-color:var(--border);}
    [data-dark="1"] .c-btn{background:var(--card);border-color:var(--border);color:var(--primary);}
    [data-dark="1"] .ld-box{background:var(--card);border-color:var(--border);}
    [data-dark="1"] .ti{background:var(--card);border-color:var(--border);}
    [data-dark="1"] .flbl{color:#CBD5E1;}
    [data-dark="1"] .btn-logout{background:var(--card);border-color:var(--border);color:var(--muted);}
    [data-dark="1"] #btn-tog{background:var(--card);border-color:var(--border);color:var(--muted);}
    [data-dark="1"] select.fc,[data-dark="1"] select.fs{background:#263148;color:var(--primary);}
  </style>
  <script>
  (function(){
    try{
      var t=localStorage.getItem('borrw_theme')||'orange';
      var d=localStorage.getItem('borrw_dark')==='1';
      var r=document.documentElement;
      var TH={orange:{a:'#F97316',ad:'#EA6A0A',nb:'rgba(249,115,22,.08)',nt:'#FB923C',fr:'rgba(249,115,22,.08)'},blue:{a:'#2563EB',ad:'#1D4ED8',nb:'rgba(37,99,235,.1)',nt:'#60A5FA',fr:'rgba(37,99,235,.1)'},green:{a:'#059669',ad:'#047857',nb:'rgba(5,150,105,.1)',nt:'#34D399',fr:'rgba(5,150,105,.1)'},dark:{a:'#7C3AED',ad:'#6D28D9',nb:'rgba(124,58,237,.1)',nt:'#A78BFA',fr:'rgba(124,58,237,.1)'}};
      var th=TH[t]||TH.orange;
      r.style.setProperty('--accent',th.a);r.style.setProperty('--accent-d',th.ad);
      r.style.setProperty('--nav-active-bg',th.nb);r.style.setProperty('--nav-active-txt',th.nt);
      r.style.setProperty('--focus-ring',th.fr);
      if(d)r.setAttribute('data-dark','1');
    }catch(e){}
  })();
  </script>
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
        <div id="app-nm" class="sb-brand-nm">{{ \App\Models\Setting::get('app_name','Sparepart MS') }}</div>
        <div class="sb-brand-sub">Management System</div>
      </div>
    </div>
    <div class="sb-nav">
      <div class="nav-sec">UTAMA</div>
      <a href="{{ route('dashboard') }}" class="nav-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 nav-ico"></i><span class="nav-txt">Dashboard</span>
      </a>
      @if(auth()->user()->isAdmin())
      <div class="nav-sec">INVENTORI</div>
      <a href="{{ route('inventory.index') }}" class="nav-btn {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam nav-ico"></i><span class="nav-txt">Inventori Barang</span>
      </a>
      <a href="{{ route('borrowers.index') }}" class="nav-btn {{ request()->routeIs('borrowers.*') ? 'active' : '' }}">
        <i class="bi bi-people nav-ico"></i><span class="nav-txt">Data Peminjam</span>
      </a>
      @endif
      <div class="nav-sec">TRANSAKSI</div>
      @if(auth()->user()->isAdmin())
      @php $pendingCnt = \App\Models\Transaction::where('status','menunggu_persetujuan')->count(); @endphp
      @endif
      <a href="{{ route('transactions.index') }}" class="nav-btn {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right nav-ico"></i>
        <span class="nav-txt" style="display:flex;align-items:center;justify-content:space-between;">
          Pinjam / Ambil
          @if(auth()->user()->isAdmin() && !empty($pendingCnt) && $pendingCnt > 0)
          <span style="background:var(--warning);color:#1E293B;padding:1px 7px;border-radius:20px;font-size:10px;font-weight:700;margin-left:6px;">{{ $pendingCnt }}</span>
          @endif
        </span>
      </a>
      <a href="{{ route('transactions.history') }}" class="nav-btn {{ request()->routeIs('transactions.history') ? 'active' : '' }}">
        <i class="bi bi-clock-history nav-ico"></i><span class="nav-txt">Riwayat Transaksi</span>
      </a>
      <a href="{{ route('returns.index') }}" class="nav-btn {{ request()->routeIs('returns.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-return-left nav-ico"></i><span class="nav-txt">Pengembalian</span>
      </a>
      @if(auth()->user()->isAdmin())
      <a href="{{ route('damaged.index') }}" class="nav-btn {{ request()->routeIs('damaged.index') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle nav-ico"></i><span class="nav-txt">Catat Rusak</span>
      </a>
      <a href="{{ route('damaged.history') }}" class="nav-btn {{ request()->routeIs('damaged.history') ? 'active' : '' }}">
        <i class="bi bi-clipboard-x nav-ico"></i><span class="nav-txt">Riwayat Rusak</span>
      </a>
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
async function apiForm(url,formData){
  try{
    const r=await fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:formData});
    const json=await r.json();
    if(!r.ok&&!json.success){throw new Error(json.message||(json.errors?Object.values(json.errors)[0][0]:'Server error'));}
    return json;
  }catch(e){
    if(e.name==='SyntaxError')return{success:false,message:'Server error'};
    throw e;
  }
}

function esc(v){if(v==null)return'';return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function fd(s){if(!s)return'—';try{return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}catch(e){return s;}}
function fdt(s){if(!s)return'—';try{return new Date(s).toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return s;}}
function nowLocal(){const d=new Date();return new Date(d-d.getTimezoneOffset()*60000).toISOString().slice(0,16);}

function statusLabel(s){
  return{aktif:'Aktif',selesai:'Selesai',partial:'Sebagian Kembali',menunggu_persetujuan:'Menunggu Konfirmasi',ditolak:'Ditolak',dipinjam:'Dipinjam',kembali:'Kembali',dipakai:'Dipakai',diambil:'Diambil',baik:'Baik',rusak:'Rusak',perlu_perbaikan:'Perlu Perbaikan',admin:'Admin',user:'User'}[s]||s;
}

function openSb(){document.getElementById('sb').classList.add('mob-open');document.getElementById('sb-ov').style.display='block';}
function closeSb(){document.getElementById('sb').classList.remove('mob-open');document.getElementById('sb-ov').style.display='none';}

@if(session('error'))
document.addEventListener('DOMContentLoaded',()=>toast({{ Js::from(session('error')) }},'danger'));
@endif

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
function photoThumb(url,label){
  if(!url)return`<span style="color:var(--muted);font-size:12px;font-style:italic;">Belum ada foto</span>`;
  return`<div style="display:inline-block;cursor:pointer;" onclick="photoPreview('${url}')" title="Klik untuk perbesar">
    <img src="${url}" alt="${label||'Foto'}" style="max-width:120px;max-height:90px;object-fit:cover;border-radius:8px;border:2px solid var(--border);display:block;transition:transform .15s;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform=''">      
    <div style="font-size:10px;color:var(--muted);text-align:center;margin-top:3px;"><i class="bi bi-arrows-fullscreen"></i> Perbesar</div>
  </div>`;
}
function photoPreview(url){
  let ov=document.getElementById('photo-preview-ov');
  if(!ov){
    ov=document.createElement('div');
    ov.id='photo-preview-ov';
    ov.style.cssText='position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.85);display:flex;align-items:center;justify-content:center;cursor:zoom-out;';
    ov.onclick=()=>ov.remove();
    document.body.appendChild(ov);
  }
  ov.innerHTML=`<img src="${url}" style="max-width:92vw;max-height:92vh;object-fit:contain;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,.6);pointer-events:none;">`;
  ov.style.display='flex';
}
/* ---- PHOTO WIDGET ----
 * Renders a photo-picker zone inside `container` (DOM element or id string).
 * Stores chosen File in `_pwFiles[key]`. Use getPhotoFile(key) to retrieve.
 * pwInit(key, container, label, required)
 */
const _pwFiles={};
function getPhotoFile(key){return _pwFiles[key]||null;}
function pwInit(key,container,label,required){
  const el=typeof container==='string'?document.getElementById(container):container;
  if(!el)return;
  el.innerHTML=_pwHTML(key,label,required);
}
function _pwHTML(key,label,required){
  return`<div class="pw-zone" id="pw-zone-${key}" onclick="_pwZoneClick('${key}')">
    <div id="pw-empty-${key}">
      <i class="bi bi-camera-fill" style="font-size:26px;color:var(--accent);display:block;margin-bottom:7px;"></i>
      <div style="font-size:13px;font-weight:600;color:var(--primary);">Ketuk untuk ambil foto</div>
      <div style="font-size:11px;color:#94A3B8;margin-top:3px;">atau <span style="color:var(--accent);cursor:pointer;" onclick="event.stopPropagation();_pwFile('${key}')">pilih dari file</span></div>
    </div>
    <div id="pw-prev-${key}" class="pw-preview" style="display:none;">
      <img id="pw-img-${key}" class="pw-img" src="" onclick="event.stopPropagation();photoPreview(this.src)">
      <div class="pw-btns">
        <button type="button" class="pw-btn-cam" onclick="event.stopPropagation();_pwCamera('${key}')"><i class="bi bi-camera-fill"></i> Ambil Ulang</button>
        <button type="button" class="pw-btn-file" onclick="event.stopPropagation();_pwFile('${key}')"><i class="bi bi-folder2-open"></i> Ganti File</button>
        <button type="button" class="pw-btn-del" onclick="event.stopPropagation();_pwClear('${key}')"><i class="bi bi-trash3"></i> Hapus</button>
      </div>
    </div>
    <input type="file" id="pw-input-${key}" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="_pwOnFile('${key}',this)">
    <input type="file" id="pw-cam-${key}" accept="image/*" capture="environment" style="display:none;" onchange="_pwOnFile('${key}',this)">
  </div>
  <div class="pw-error" id="pw-err-${key}">Foto wajib diisi.</div>`;
}
function _pwZoneClick(key){
  if(!_pwFiles[key]) _pwCamera(key);
}
function _pwCamera(key){document.getElementById('pw-cam-'+key).click();}
function _pwFile(key){document.getElementById('pw-input-'+key).click();}
function _pwCompressImage(file,maxDim,quality){
  return new Promise(function(resolve){
    var reader=new FileReader();
    reader.onload=function(e){
      var img=new Image();
      img.onload=function(){
        var w=img.width,h=img.height;
        if(w>maxDim||h>maxDim){
          if(w>=h){h=Math.round(h*maxDim/w);w=maxDim;}
          else{w=Math.round(w*maxDim/h);h=maxDim;}
        }
        var cv=document.createElement('canvas');
        cv.width=w;cv.height=h;
        var ctx=cv.getContext('2d');
        ctx.drawImage(img,0,0,w,h);
        cv.toBlob(function(blob){
          if(blob){
            var name=file.name.replace(/\.[^.]+$/,'.jpg');
            resolve(new File([blob],name,{type:'image/jpeg',lastModified:Date.now()}));
          } else {
            resolve(file);
          }
        },'image/jpeg',quality);
      };
      img.onerror=function(){resolve(file);};
      img.src=e.target.result;
    };
    reader.onerror=function(){resolve(file);};
    reader.readAsDataURL(file);
  });
}
async function _pwOnFile(key,inp){
  if(!inp.files||!inp.files[0])return;
  const raw=inp.files[0];
  const file=await _pwCompressImage(raw,1280,0.78);
  _pwFiles[key]=file;
  const url=URL.createObjectURL(file);
  document.getElementById('pw-img-'+key).src=url;
  document.getElementById('pw-empty-'+key).style.display='none';
  document.getElementById('pw-prev-'+key).style.display='flex';
  document.getElementById('pw-zone-'+key).classList.add('has-photo');
  document.getElementById('pw-err-'+key).style.display='none';
  inp.value='';
}
function _pwClear(key){
  delete _pwFiles[key];
  document.getElementById('pw-input-'+key).value='';
  document.getElementById('pw-cam-'+key).value='';
  document.getElementById('pw-img-'+key).src='';
  document.getElementById('pw-empty-'+key).style.display='';
  document.getElementById('pw-prev-'+key).style.display='none';
  document.getElementById('pw-zone-'+key).classList.remove('has-photo');
}
function pwValidate(key,label){
  const err=document.getElementById('pw-err-'+key);
  if(!_pwFiles[key]){if(err)err.style.display='block';toast((label||'Foto')+' wajib diisi!','warning');return false;}
  if(err)err.style.display='none';
  return true;
}
</script>
<script>
(function(){
  var t=parseInt('{{ \App\Models\Setting::get("session_timeout","30") }}')*60000;
  if(!t||t<60000)return;
  var timer;
  function reset(){clearTimeout(timer);timer=setTimeout(function(){fetch('{{ route("logout") }}',{method:'POST',headers:{'X-CSRF-TOKEN':CSRF}}).finally(function(){window.location.href='{{ route("login") }}';});},t);}
  ['mousedown','mousemove','keydown','scroll','touchstart','click'].forEach(function(e){document.addEventListener(e,reset,{passive:true});});
  reset();
})();
</script>
@stack('scripts')
</body>
</html>
