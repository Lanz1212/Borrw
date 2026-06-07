@extends('layouts.app')
@section('title','Riwayat Transaksi')
@section('page-title','Riwayat Transaksi')

@section('content')
<!-- Transaction Detail Modal -->
<div class="modal fade" id="mdl-trx-detail" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-clipboard2-data"></i> Detail Transaksi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mdl-trx-body"></div>
    </div>
  </div>
</div>

<div class="card p16 mb-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="sw"><i class="bi bi-search"></i><input type="text" class="fc" id="h-q" placeholder="Cari peminjam / ID transaksi..." oninput="filterHistory()"></div>
    </div>
    <div class="col-6 col-sm-auto">
      <select class="fs" id="h-st" onchange="filterHistory()" style="min-width:150px;">
        <option value="">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="partial">Sebagian Kembali</option>
        <option value="selesai">Selesai</option>
      </select>
    </div>
    <div class="col-6 col-sm-auto">
      <input type="date" id="h-date" class="fs" onchange="filterHistory()" title="Filter tanggal pinjam">
    </div>
    @if(auth()->user()->isAdmin())
    <div class="col-auto">
      <button class="b-xl" onclick="exportHistory()"><i class="bi bi-file-earmark-excel"></i> Export</button>
    </div>
    @endif
    <div class="col-12 col-sm-auto">
      <a href="{{ route('transactions.index') }}" class="b-acc w-100"><i class="bi bi-plus-lg"></i> Transaksi Baru</a>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-6 col-lg-3"><div class="stat-card sc-blue"><div class="stat-ico si-blue"><i class="bi bi-list-ul"></i></div><div class="stat-val" id="h-count">—</div><div class="stat-lbl">Total Transaksi</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-orange"><div class="stat-ico si-orange"><i class="bi bi-clock-fill"></i></div><div class="stat-val" id="h-aktif">—</div><div class="stat-lbl">Aktif</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-green"><div class="stat-ico si-green"><i class="bi bi-check-circle-fill"></i></div><div class="stat-val" id="h-done">—</div><div class="stat-lbl">Selesai</div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card sc-red"><div class="stat-ico si-red"><i class="bi bi-arrow-return-right"></i></div><div class="stat-val" id="h-partial">—</div><div class="stat-lbl">Sebagian Kembali</div></div></div>
</div>

<div class="tw">
  <table class="table table-hover">
    <thead><tr>
      <th>ID Transaksi</th><th>Peminjam</th><th>Diproses Oleh</th>
      <th>Tgl Pinjam</th><th>Tgl Kembali</th>
      <th style="text-align:center;">Item</th><th>Status</th>
    </tr></thead>
    <tbody id="hist-tb"><tr><td colspan="7" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
  </table>
</div>
@endsection

@push('scripts')
<script>
let _hist = [], _histFiltered = [];

async function loadHistory(){
  ld(true);
  try{
    const res = await api('{{ route("transactions.data") }}');
    ld(false);
    _hist = res.data || [];
    filterHistory();
  }catch(e){ld(false);toast(e.message,'danger');}
}

function filterHistory(){
  const q    = (document.getElementById('h-q').value||'').toLowerCase().trim();
  const st   = document.getElementById('h-st').value;
  const date = document.getElementById('h-date').value;
  _histFiltered = _hist.filter(t=>{
    const matchQ = !q || (t.transaction_code||'').toLowerCase().includes(q) || (t.borrower_name||'').toLowerCase().includes(q) || (t.created_by_name||'').toLowerCase().includes(q);
    const matchSt = !st || t.status === st;
    let matchDate = true;
    if(date){
      const td = t.loan_date ? new Date(t.loan_date) : null;
      if(td){
        const ds = td.getFullYear()+'-'+String(td.getMonth()+1).padStart(2,'0')+'-'+String(td.getDate()).padStart(2,'0');
        matchDate = ds === date;
      } else { matchDate = false; }
    }
    return matchQ && matchSt && matchDate;
  });

  const total   = _histFiltered.length;
  const aktif   = _histFiltered.filter(t=>t.status==='aktif').length;
  const done    = _histFiltered.filter(t=>t.status==='selesai').length;
  const partial = _histFiltered.filter(t=>t.status==='partial').length;
  document.getElementById('h-count').textContent  = total;
  document.getElementById('h-aktif').textContent  = aktif;
  document.getElementById('h-done').textContent   = done;
  document.getElementById('h-partial').textContent = partial;

  renderHistory(_histFiltered);
}

function renderHistory(list){
  const tb = document.getElementById('hist-tb');
  if(!list.length){
    tb.innerHTML=`<tr><td colspan="7"><div class="empty"><div class="ei"><i class="bi bi-clock-history"></i></div><p>Tidak ada data transaksi</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = list.map((t,idx)=>`<tr class="tr-click" onclick="showDetail(${idx})" title="Klik untuk detail">
    <td><code style="font-size:10px;">${esc(t.transaction_code)}</code></td>
    <td style="font-weight:500;">${esc(t.borrower_name)}</td>
    <td style="color:var(--muted);font-size:12px;">${esc(t.created_by_name||'—')}</td>
    <td>${fdt(t.loan_date)}</td>
    <td>${t.return_date ? fdt(t.return_date) : '<span style="color:var(--muted);">—</span>'}</td>
    <td style="text-align:center;">${t.details ? t.details.length : 0}</td>
    <td><span class="bdg b-${esc(t.status)}">${esc(statusLabel(t.status))}</span></td>
  </tr>`).join('');
}

function showDetail(idx){
  const t = _histFiltered[idx]; if(!t) return;
  const detailRows = (t.details||[]).map(d=>{
    const ret=d.qty_returned>0;
    const isBon=d.item_type==='bon';
    const isConsumable=d.item_type==='consumable';
    const good=d.qty_good??0, cons=d.qty_consumed??0, dmg=d.qty_damaged??0, lost=d.qty_lost??0;
    const typeBadge=isBon
      ?`<span class="bdg b-bon"><i class="bi bi-tag"></i> BON</span>`
      :(d.item_type==='pinjam'?`<span class="bdg b-pinjam"><i class="bi bi-arrow-repeat"></i> Pinjam</span>`:`<span class="bdg b-consumable"><i class="bi bi-fire"></i> Consumable</span>`);
    const dipakaiVal=isConsumable?d.qty:(isBon?(ret?cons:'—'):'—');
    const dipakaiClr=isConsumable?'#C2410C':(cons>0?'#C2410C':'var(--muted)');
    return `<tr>
    <td style="font-weight:500;white-space:normal;min-width:140px;">${esc(d.item_name)}</td>
    <td><code style="font-size:10px;">${esc(d.item_code)}</code></td>
    <td>${typeBadge}</td>
    <td style="text-align:center;">${d.qty}</td>
    <td style="text-align:center;font-weight:600;color:var(--success);">${ret?good:'—'}</td>
    <td style="text-align:center;font-weight:700;color:${dipakaiClr};">${dipakaiVal}</td>
    <td style="text-align:center;font-weight:${dmg>0?'700':'400'};color:${dmg>0?'var(--danger)':'var(--muted)'}">${ret?dmg:'—'}</td>
    <td style="text-align:center;font-weight:${lost>0?'700':'400'};color:${lost>0?'var(--danger)':'var(--muted)'}">${ret?lost:'—'}</td>
    <td><span class="bdg b-${esc(d.status)}">${esc(statusLabel(d.status))}</span></td>
  </tr>${d.return_notes?`<tr><td colspan="9" style="padding:3px 12px 8px;background:rgba(249,115,22,.04);"><div style="font-size:11.5px;color:var(--muted);display:flex;align-items:flex-start;gap:5px;"><i class="bi bi-chat-left-text-fill" style="color:var(--accent);flex-shrink:0;margin-top:2px;"></i><span>${esc(d.return_notes)}</span></div></td></tr>`:''}`;
  }).join('') || `<tr><td colspan="9" class="text-center" style="color:var(--muted);padding:16px;">Tidak ada item</td></tr>`;

  document.getElementById('mdl-trx-body').innerHTML = `
    <div class="detail-grid">
      <div><div class="dg-lbl">ID Transaksi</div><code style="font-size:13px;word-break:break-all;">${esc(t.transaction_code)}</code></div>
      <div><div class="dg-lbl">Status</div><span class="bdg b-${esc(t.status)}">${esc(statusLabel(t.status))}</span></div>
      <div><div class="dg-lbl">Peminjam</div><div class="dg-val">${esc(t.borrower_name)}</div></div>
      <div><div class="dg-lbl">Diproses Oleh</div><div class="dg-val">${esc(t.created_by_name||'—')}</div></div>
      <div><div class="dg-lbl">Tanggal Pinjam</div><div class="dg-val">${fdt(t.loan_date)}</div></div>
      <div><div class="dg-lbl">Tanggal Kembali</div><div class="dg-val">${t.return_date ? fdt(t.return_date) : '<span style="color:var(--muted);font-size:12px;">Belum dikembalikan</span>'}</div></div>
      ${t.notes?`<div class="dg-full"><div class="dg-lbl">Catatan</div><div class="dg-val" style="background:var(--bg);padding:8px 12px;border-radius:8px;">${esc(t.notes)}</div></div>`:''}
    </div>
    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:14px;">
      <div>
        <div class="dg-lbl" style="font-size:11px;font-weight:600;margin-bottom:6px;">FOTO SAAT PEMINJAMAN</div>
        ${photoThumb(t.borrow_photo_url,'Foto Peminjaman')}
      </div>
      ${(()=>{const rp=(t.details||[]).flatMap(d=>d.return_photos||[]);return rp.length?`<div><div class="dg-lbl" style="font-size:11px;font-weight:600;margin-bottom:6px;">FOTO SAAT PENGEMBALIAN</div><div style="display:flex;gap:8px;flex-wrap:wrap;">${rp.map(r=>r.return_photo_url?`<div style="text-align:center;">${photoThumb(r.return_photo_url,'Foto Kembali')}${r.damage_photo_url?`<div style="margin-top:4px;">${photoThumb(r.damage_photo_url,'Foto Rusak')}</div>`:''}</div>`:'').join('')}</div></div>`:`<div><div class="dg-lbl" style="font-size:11px;font-weight:600;margin-bottom:6px;">FOTO SAAT PENGEMBALIAN</div><span style="color:var(--muted);font-size:12px;font-style:italic;">Belum ada foto pengembalian</span></div>`;})()}
    </div>
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;margin-top:20px;"><i class="bi bi-box-seam text-primary"></i> Daftar Barang</div>
    <div class="tw"><table class="table">
      <thead><tr><th>Nama Barang</th><th>Kode</th><th>Jenis</th><th style="text-align:center;">Jml</th><th style="text-align:center;">Kembali</th><th style="text-align:center;">Dipakai*</th><th style="text-align:center;">Rusak</th><th style="text-align:center;">Hilang</th><th>Status</th></tr></thead>
      <tbody>${detailRows}</tbody>
    </table></div>`;
  new bootstrap.Modal(document.getElementById('mdl-trx-detail')).show();
}

function exportHistory(){
  if(!_histFiltered.length){toast('Tidak ada data untuk diekspor.','warning');return;}
  const rows=[
    [
      'ID Transaksi',
      'Peminjam',
      'Diproses Oleh',
      'Tanggal Pinjam',
      'Kode Barang',
      'Nama Barang',
      'Jenis',
      'Jumlah Pinjam',
      'Kembali',
      'Dipakai (BON)',
      'Rusak',
      'Hilang',
      'Status Barang',
      'Tanggal Kembali',
      'Catatan Pengembalian',
      'Status Transaksi',
      'Catatan Transaksi',
    ]
  ];

  _histFiltered.forEach(t=>{
    const details = t.details || [];
    if(!details.length){
      // Transaksi tanpa detail barang — tetap masukkan 1 baris
      rows.push([
        t.transaction_code || '',
        t.borrower_name    || '',
        t.created_by_name  || '',
        t.loan_date ? new Date(t.loan_date).toLocaleDateString('id-ID') : '',
        '', '', '', '', '', '', '', '', '', '',
        t.return_date ? new Date(t.return_date).toLocaleDateString('id-ID') : '',
        statusLabel(t.status),
        t.notes || '',
      ]);
    } else {
      details.forEach(d=>{
        const returned = d.qty_returned > 0;
        rows.push([
          t.transaction_code          || '',
          t.borrower_name             || '',
          t.created_by_name           || '',
          t.loan_date ? new Date(t.loan_date).toLocaleDateString('id-ID') : '',
          d.item_code                 || '',
          d.item_name                 || '',
          d.item_type === 'pinjam' ? 'Pinjam' : d.item_type === 'bon' ? 'BON' : 'Consumable',
          d.qty,
          returned ? (d.qty_good  ?? 0) : '',
          d.item_type === 'bon' ? (returned ? (d.qty_consumed ?? 0) : '') : '',
          returned ? (d.qty_damaged ?? 0) : '',
          returned ? (d.qty_lost  ?? 0) : '',
          statusLabel(d.status),
          d.return_date ? new Date(d.return_date).toLocaleDateString('id-ID') : '',
          d.return_notes || '',
          statusLabel(t.status),
          t.notes || '',
        ]);
      });
    }
  });

  exportXlsx(rows,'Riwayat_Transaksi_'+dateStr()+'.xlsx');
  toast('Export berhasil diunduh.');
}

loadHistory();
</script>
@endpush
