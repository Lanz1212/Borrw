<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Label — {{ $inventory->code }}</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f8fafc;display:block;text-align:center;padding:24px 16px 80px;}
    .label{display:inline-block;width:200px;border:2px solid #1E293B;border-radius:10px;padding:14px 12px;text-align:center;background:#fff;}
    .qr-box{background:#fff;padding:4px;display:inline-block;border:1px solid #e2e8f0;border-radius:4px;margin-bottom:10px;}
    .qr-box svg{width:164px;height:164px;display:block;}
    .divider{border:none;border-top:1.5px dashed #e2e8f0;margin:8px 0;}
    .item-name{font-size:12px;font-weight:700;color:#1E293B;margin-bottom:3px;line-height:1.3;word-break:break-word;}
    .item-code{font-family:monospace;font-size:14px;font-weight:700;color:#F97316;letter-spacing:1px;margin-bottom:5px;}
    .item-meta{font-size:9.5px;color:#64748B;line-height:1.6;}
    .badge{display:inline-block;padding:2px 7px;border-radius:20px;font-size:9px;font-weight:600;margin-top:3px;}
    .badge-pinjam{background:rgba(59,130,246,.12);color:#1D4ED8;}
    .badge-consumable{background:rgba(245,158,11,.12);color:#92400E;}
    .print-btn{position:fixed;bottom:20px;right:20px;background:#1E293B;color:#fff;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(0,0,0,.2);}
    .print-btn:hover{background:#334155;}
    @media print{
      html,body{margin:0!important;padding:0!important;background:#fff!important;display:block!important;}
      .label{border-radius:6px;}
      .print-btn{display:none!important;}
      @page{size:72mm 90mm;margin:3mm;}
    }
  </style>
</head>
<body>

<div class="label">
  <div class="qr-box">
    {!! $qrSvg !!}
  </div>
  <hr class="divider">
  <div class="item-name">{{ $inventory->name }}</div>
  <div class="item-code">{{ $inventory->code }}</div>
  <div class="item-meta">
    {{ $inventory->category }}<br>
    <span class="badge {{ $inventory->type === 'pinjam' ? 'badge-pinjam' : 'badge-consumable' }}">
      {{ $inventory->type === 'pinjam' ? 'Pinjam' : 'Consumable' }}
    </span>
  </div>
</div>

<button class="print-btn" onclick="window.print()">
  Print / Simpan PDF
</button>

<script>
  // Auto-trigger print on load for PDF save workflow
  window.addEventListener('load', function(){
    // Small delay to ensure QR renders
    setTimeout(function(){
      // Don't auto-print, let user click the button
    }, 300);
  });
</script>
</body>
</html>
