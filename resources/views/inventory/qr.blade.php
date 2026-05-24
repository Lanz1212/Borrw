@extends('layouts.app')
@section('title','QR Code — '.$inventory->code)
@section('page-title','QR Code Barang')

@push('styles')
<style>
  .qr-wrap{max-width:420px;margin:0 auto;}
  .qr-card-inner{background:#fff;border:1px solid var(--border);border-radius:16px;padding:30px 24px;text-align:center;}
  .qr-svg-box{display:inline-block;background:#fff;padding:12px;border:2px solid #1E293B;border-radius:12px;margin-bottom:18px;}
  .qr-svg-box svg{width:260px;height:260px;display:block;}
  .qr-item-name{font-size:20px;font-weight:700;color:var(--primary);margin-bottom:4px;}
  .qr-item-code{font-family:monospace;font-size:17px;font-weight:700;color:var(--accent);letter-spacing:1.5px;margin-bottom:10px;}
  .qr-meta{display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:6px;}
  .qr-hint{font-size:11px;color:var(--muted);margin-top:8px;}
  .btn-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:20px;}
  @media print{
    #topbar,#sb,#sb-ov,.sh,.qr-back,.btn-actions,nav{display:none!important;}
    #main{margin:0!important;width:100%!important;}
    #pg{padding:0!important;}
    .qr-wrap{max-width:100%;}
    body{background:#fff;}
  }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-3 qr-back">
  <a href="{{ route('inventory.index') }}" class="b-out"><i class="bi bi-arrow-left"></i> Inventori</a>
</div>

<div class="qr-wrap">
  <div class="qr-card-inner">
    <div class="qr-svg-box">
      {!! $qrSvg !!}
    </div>
    <div class="qr-item-name">{{ $inventory->name }}</div>
    <div class="qr-item-code">{{ $inventory->code }}</div>
    <div class="qr-meta">
      <span class="bdg b-cat">{{ $inventory->category }}</span>
      <span class="bdg {{ $inventory->type === 'pinjam' ? 'b-pinjam' : 'b-consumable' }}">
        <i class="bi {{ $inventory->type === 'pinjam' ? 'bi-arrow-repeat' : 'bi-fire' }}"></i>
        {{ $inventory->type === 'pinjam' ? 'Pinjam' : 'Consumable' }}
      </span>
    </div>
    <div class="qr-hint"><i class="bi bi-info-circle me-1"></i>QR berisi kode barang: <strong>{{ $inventory->code }}</strong> — digunakan untuk scan peminjaman</div>
  </div>

  <div class="btn-actions">
    <button class="b-out" onclick="window.print()">
      <i class="bi bi-printer"></i> Print
    </button>
    <a href="{{ route('inventory.qr.print', $inventory) }}" target="_blank" class="b-pri">
      <i class="bi bi-file-earmark-pdf"></i> Print / Simpan PDF
    </a>
  </div>
</div>
@endsection
