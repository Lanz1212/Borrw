@extends('layouts.app')
@section('title','Barang Rusak')
@section('page-title','Barang Rusak')

@section('content')
<!-- Damaged Detail Modal -->
<div class="modal fade" id="mdl-dmg-detail" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-wrench-adjustable"></i> Detail Barang Rusak</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mdl-dmg-body"></div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Left: Report Form -->
  <div class="col-12 col-lg-4">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-exclamation-triangle text-warning"></i> Catat Barang Rusak</div>
      <div class="fgrp">
        <label class="flbl">Pilih Barang *</label>
        <select id="d-item" class="fs w-100"><option value="">— Pilih barang —</option></select>
      </div>
      <div class="fgrp">
        <label class="flbl">Jumlah Rusak *</label>
        <input type="number" id="d-qty" class="fc w-100" min="1" value="1">
      </div>
      <div class="fgrp">
        <label class="flbl">Keterangan *</label>
        <textarea id="d-desc" class="fc w-100" rows="3" placeholder="Penyebab kerusakan..."></textarea>
      </div>
      <button class="b-acc w-100" onclick="saveDmg()"><i class="bi bi-check-circle"></i> Catat Kerusakan</button>
    </div>
  </div>

  <!-- Right: History -->
  <div class="col-12 col-lg-8">
    <div class="card p20">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="st"><i class="bi bi-card-list text-primary"></i> Riwayat Barang Rusak</div>
        <a href="{{ route('damaged.history') }}" class="b-out" style="font-size:12px;padding:6px 12px;"><i class="bi bi-list-ul"></i> Lihat Semua</a>
      </div>
      <p style="font-size:12px;color:var(--muted);margin-bottom:12px;"><i class="bi bi-info-circle"></i> Klik baris untuk detail</p>
      <div class="tw">
        <table class="table table-hover">
          <thead><tr><th>Tanggal</th><th>Nama Barang</th><th style="text-align:center;">Jml</th><th>Keterangan</th><th>Oleh</th></tr></thead>
          <tbody id="dmg-tb"><tr><td colspan="5" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let _dmgStore = [];

async function loadDmg(){
  ld(true);
  try{
    const [dmgR, invR] = await Promise.all([
      api('{{ route("damaged.data") }}'),
      api('{{ route("inventory.data") }}'),
    ]);
    ld(false);
    _dmgStore = dmgR.data || [];
    const invL = invR.data || [];

    const sel = document.getElementById('d-item');
    sel.innerHTML = '<option value="">— Pilih barang —</option>' +
      invL.map(i=>`<option value="${i.id}">${esc(i.code)} — ${esc(i.name)} (stok: ${i.available_qty})</option>`).join('');

    renderDmgRows(_dmgStore);
  }catch(e){ld(false);toast(e.message,'danger');}
}

function renderDmgRows(items){
  const tb=document.getElementById('dmg-tb');
  if(!items.length){
    tb.innerHTML=`<tr><td colspan="5"><div class="empty"><div class="ei"><i class="bi bi-wrench-adjustable"></i></div><p>Belum ada catatan</p></div></td></tr>`;
    return;
  }
  tb.innerHTML=items.map((d,idx)=>`<tr class="tr-click" onclick="showDmgDetail(${idx})">
    <td>${fdt(d.date)}</td>
    <td style="font-weight:500;">${esc(d.item_name)}</td>
    <td style="text-align:center;color:var(--danger);font-weight:700;">${d.qty}</td>
    <td style="max-width:200px;white-space:normal;">${esc(d.description||'—')}</td>
    <td>${esc(d.reported_by_name||'—')}</td>
  </tr>`).join('');
}

function showDmgDetail(idx){
  const d=_dmgStore[idx];if(!d)return;
  const fromTrx=d.transaction_id&&d.transaction_id!=='';
  document.getElementById('mdl-dmg-body').innerHTML=`
    <div class="detail-grid">
      <div><div class="dg-lbl">Nama Barang</div><div class="dg-val" style="font-size:16px;">${esc(d.item_name)}</div></div>
      <div><div class="dg-lbl">Jumlah Rusak</div><div class="dg-val" style="font-size:22px;color:var(--danger);font-weight:700;">${d.qty} unit</div></div>
      <div><div class="dg-lbl">Tanggal Dicatat</div><div class="dg-val">${fdt(d.date)}</div></div>
      <div><div class="dg-lbl">Dicatat Oleh</div><div class="dg-val">${esc(d.reported_by_name||'—')}</div></div>
      <div class="dg-full"><div class="dg-lbl">Keterangan</div><div class="dg-val" style="background:var(--bg);padding:10px 14px;border-radius:9px;line-height:1.5;">${esc(d.description||'—')}</div></div>
    </div>
    ${fromTrx?`
    <div style="background:rgba(62,207,255,.07);border:1px solid rgba(62,207,255,.25);border-radius:10px;padding:14px;margin-top:4px;">
      <div style="font-weight:700;font-size:13px;margin-bottom:10px;"><i class="bi bi-arrow-return-left"></i> Asal: Dari Pengembalian Transaksi</div>
      <div class="detail-grid" style="margin-bottom:0;">
        <div><div class="dg-lbl">ID Transaksi</div><code style="font-size:12px;word-break:break-all;">${esc(d.transaction_code||'—')}</code></div>
        <div><div class="dg-lbl">Peminjam</div><div class="dg-val">${esc(d.borrower_name||'—')}</div></div>
        <div class="dg-full"><div class="dg-lbl">Tanggal Pinjam</div><div class="dg-val">${fdt(d.loan_date)}</div></div>
      </div>
    </div>`:`
    <div style="background:var(--bg);border-radius:10px;padding:12px 14px;font-size:13px;color:var(--muted);">
      <i class="bi bi-pencil-square me-1"></i> Sumber: <strong>Input manual</strong> (bukan dari pengembalian)
    </div>`}`;
  new bootstrap.Modal(document.getElementById('mdl-dmg-detail')).show();
}

async function saveDmg(){
  const data={
    inventory_id:document.getElementById('d-item').value,
    qty:document.getElementById('d-qty').value,
    description:document.getElementById('d-desc').value.trim()
  };
  if(!data.inventory_id){toast('Pilih barang!','warning');return;}
  if(!data.description){toast('Keterangan wajib diisi!','warning');return;}
  if(parseInt(data.qty)<1){toast('Jumlah minimal 1!','warning');return;}
  ld(true);
  try{
    const res=await api('{{ route("damaged.store") }}','POST',data);
    ld(false);toast(res.message);
    document.getElementById('d-qty').value='1';
    document.getElementById('d-desc').value='';
    document.getElementById('d-item').value='';
    loadDmg();
  }catch(e){ld(false);toast(e.message,'danger');}
}

loadDmg();
</script>
@endpush
