@extends('layouts.app')
@section('title','Riwayat Barang Rusak')
@section('page-title','Riwayat Barang Rusak')

@section('content')
<!-- Detail Modal -->
<div class="modal fade" id="mdl-dmg-detail" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-clipboard-x"></i> Detail Barang Rusak</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mdl-dmg-body"></div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card p16 mb-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="sw"><i class="bi bi-search"></i>
        <input type="text" class="fc" id="dh-q" placeholder="Cari nama barang / keterangan..." oninput="filterDmgH()">
      </div>
    </div>
    <div class="col-6 col-sm-auto">
      <select class="fs" id="dh-src" onchange="filterDmgH()" style="min-width:160px;">
        <option value="">Semua Sumber</option>
        <option value="manual">Manual</option>
        <option value="return">Dari Pengembalian</option>
      </select>
    </div>
    <div class="col-6 col-sm-auto">
      <input type="date" id="dh-date" class="fs" onchange="filterDmgH()" title="Filter tanggal">
    </div>
    <div class="col-auto">
      <button class="b-xl" onclick="exportDmgH()"><i class="bi bi-file-earmark-excel"></i> Export</button>
    </div>
    <div class="col-12 col-sm-auto">
      <a href="{{ route('damaged.index') }}" class="b-acc w-100"><i class="bi bi-plus-lg"></i> Catat Kerusakan</a>
    </div>
  </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
  <div class="col-6 col-lg-3"><div class="stat-card sc-red"><div class="stat-ico si-red"><i class="bi bi-clipboard-x-fill"></i></div><div class="stat-val" id="dh-cnt">—</div><div class="stat-lbl">Total Catatan</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-orange"><div class="stat-ico si-orange"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-val" id="dh-qty">—</div><div class="stat-lbl">Total Unit Rusak</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-blue"><div class="stat-ico si-blue"><i class="bi bi-pencil-square"></i></div><div class="stat-val" id="dh-manual">—</div><div class="stat-lbl">Input Manual</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-purple"><div class="stat-ico si-purple"><i class="bi bi-arrow-return-left"></i></div><div class="stat-val" id="dh-ret">—</div><div class="stat-lbl">Dari Pengembalian</div></div></div>
</div>

<!-- Table -->
<div class="tw">
  <table class="table table-hover">
    <thead><tr>
      <th>Tanggal</th><th>Nama Barang</th><th style="text-align:center;">Jumlah</th>
      <th>Keterangan</th><th>Dicatat Oleh</th><th>Sumber</th>
    </tr></thead>
    <tbody id="dmgh-tb"><tr><td colspan="6" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
  </table>
</div>
@endsection

@push('scripts')
<script>
let _dmgAll = [], _dmgFiltered = [];

async function loadDmgH(){
  ld(true);
  try{
    const res = await api('{{ route("damaged.data") }}');
    ld(false);
    _dmgAll = res.data || [];
    filterDmgH();
  }catch(e){ld(false);toast(e.message,'danger');}
}

function filterDmgH(){
  const q    = (document.getElementById('dh-q').value||'').toLowerCase().trim();
  const src  = document.getElementById('dh-src').value;
  const date = document.getElementById('dh-date').value;

  _dmgFiltered = _dmgAll.filter(d=>{
    const matchQ  = !q  || (d.item_name||'').toLowerCase().includes(q) || (d.description||'').toLowerCase().includes(q) || (d.reported_by_name||'').toLowerCase().includes(q);
    const isRet   = d.transaction_id && d.transaction_id !== '';
    const matchSrc = !src || (src==='manual'?!isRet:isRet);
    let matchDate = true;
    if(date){
      const td = d.date ? new Date(d.date) : null;
      if(td){
        const ds = td.getFullYear()+'-'+String(td.getMonth()+1).padStart(2,'0')+'-'+String(td.getDate()).padStart(2,'0');
        matchDate = ds === date;
      } else { matchDate = false; }
    }
    return matchQ && matchSrc && matchDate;
  });

  const cnt    = _dmgFiltered.length;
  const qty    = _dmgFiltered.reduce((s,d)=>s+(d.qty||0),0);
  const manual = _dmgFiltered.filter(d=>!d.transaction_id).length;
  const ret    = _dmgFiltered.filter(d=>d.transaction_id).length;

  document.getElementById('dh-cnt').textContent    = cnt;
  document.getElementById('dh-qty').textContent    = qty;
  document.getElementById('dh-manual').textContent = manual;
  document.getElementById('dh-ret').textContent    = ret;

  renderDmgH(_dmgFiltered);
}

function renderDmgH(list){
  const tb = document.getElementById('dmgh-tb');
  if(!list.length){
    tb.innerHTML=`<tr><td colspan="6"><div class="empty"><div class="ei"><i class="bi bi-clipboard-x"></i></div><p>Tidak ada data barang rusak</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = list.map((d,idx)=>`<tr class="tr-click" onclick="showDmgDetail(${idx})" title="Klik untuk detail">
    <td>${fdt(d.date)}</td>
    <td style="font-weight:500;">${esc(d.item_name)}</td>
    <td style="text-align:center;color:var(--danger);font-weight:700;">${d.qty}</td>
    <td style="max-width:200px;white-space:normal;font-size:12px;color:var(--muted);">${esc((d.description||'—').substring(0,60))}</td>
    <td style="font-size:12px;">${esc(d.reported_by_name||'—')}</td>
    <td>${d.transaction_id
      ? '<span class="bdg b-pinjam"><i class="bi bi-arrow-return-left"></i> Pengembalian</span>'
      : '<span class="bdg b-cat"><i class="bi bi-pencil-square"></i> Manual</span>'
    }</td>
  </tr>`).join('');
}

function showDmgDetail(idx){
  const d = _dmgFiltered[idx]; if(!d) return;
  const fromTrx = d.transaction_id && d.transaction_id!=='';
  document.getElementById('mdl-dmg-body').innerHTML=`
    <div class="detail-grid">
      <div><div class="dg-lbl">Nama Barang</div><div class="dg-val" style="font-size:16px;font-weight:600;">${esc(d.item_name)}</div></div>
      <div><div class="dg-lbl">Jumlah Rusak</div><div class="dg-val" style="font-size:24px;color:var(--danger);font-weight:800;">${d.qty} <span style="font-size:14px;font-weight:500;">unit</span></div></div>
      <div><div class="dg-lbl">Tanggal Dicatat</div><div class="dg-val">${fdt(d.date)}</div></div>
      <div><div class="dg-lbl">Dicatat Oleh</div><div class="dg-val">${esc(d.reported_by_name||'—')}</div></div>
      <div class="dg-full"><div class="dg-lbl">Keterangan</div><div class="dg-val" style="background:var(--bg);padding:10px 14px;border-radius:9px;line-height:1.6;white-space:pre-wrap;">${esc(d.description||'—')}</div></div>
    </div>
    ${fromTrx?`
    <div style="background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2);border-radius:10px;padding:14px;margin-top:4px;">
      <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:var(--info);"><i class="bi bi-arrow-return-left"></i> Sumber: Dari Pengembalian Transaksi</div>
      <div class="detail-grid" style="margin-bottom:0;">
        <div><div class="dg-lbl">ID Transaksi</div><code style="font-size:12px;word-break:break-all;">${esc(d.transaction_code||'—')}</code></div>
        <div><div class="dg-lbl">Peminjam</div><div class="dg-val">${esc(d.borrower_name||'—')}</div></div>
        <div><div class="dg-lbl">Tanggal Pinjam</div><div class="dg-val">${fdt(d.loan_date)}</div></div>
        <div><div class="dg-lbl">Catatan Kondisi</div><div class="dg-val">${d.condition_notes?esc(d.condition_notes):'<span style="color:var(--muted);font-style:italic;font-size:12px;">—</span>'}</div></div>
      </div>
    </div>`:`
    <div style="background:var(--bg);border-radius:10px;padding:12px 14px;font-size:13px;color:var(--muted);margin-top:4px;">
      <i class="bi bi-pencil-square me-1"></i> Sumber: <strong>Input manual</strong>
    </div>`}`;
  new bootstrap.Modal(document.getElementById('mdl-dmg-detail')).show();
}

function exportDmgH(){
  if(!_dmgFiltered.length){toast('Tidak ada data untuk diekspor.','warning');return;}
  const rows=[
    [
      'Tanggal Dicatat',
      'Kode Barang',
      'Nama Barang',
      'Jumlah Rusak',
      'Sumber',
      'ID Transaksi',
      'Peminjam',
      'Tanggal Pinjam',
      'Catatan Kondisi',
      'Keterangan',
      'Dicatat Oleh',
    ],
    ..._dmgFiltered.map(d=>{
      const fromTrx = d.transaction_id && d.transaction_id !== '';
      return [
        d.date ? new Date(d.date).toLocaleDateString('id-ID') : '',
        d.item_code || '',
        d.item_name || '',
        d.qty,
        fromTrx ? 'Dari Pengembalian' : 'Manual',
        fromTrx ? (d.transaction_code || '') : '',
        fromTrx ? (d.borrower_name   || '') : '',
        fromTrx && d.loan_date ? new Date(d.loan_date).toLocaleDateString('id-ID') : '',
        d.condition_notes || '',
        d.description     || '',
        d.reported_by_name || '',
      ];
    })
  ];
  exportXlsx(rows,'Riwayat_Rusak_'+dateStr()+'.xlsx');
  toast('Export berhasil diunduh.');
}

loadDmgH();
</script>
@endpush
