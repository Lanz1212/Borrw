@extends('layouts.app')
@section('title','Transaksi Pinjam / Ambil')
@section('page-title','Transaksi Pinjam / Ambil')

@section('content')
<!-- Transaction Detail Modal (global) -->
<div class="modal fade" id="mdl-trx-detail" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-clipboard2-data"></i> Detail Transaksi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mdl-trx-body"></div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Left: New Transaction Form -->
  <div class="col-12 col-xl-5">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-cart3" style="color:var(--accent);"></i> Transaksi Baru</div>

      <div class="fgrp">
        <label class="flbl">Peminjam / Pengguna *</label>
        <div class="sg-wrap w-100">
          <input type="text" id="t-brw-txt" class="fc w-100" placeholder="Ketik nama peminjam..." autocomplete="off" oninput="sgBrwInput(this.value)" onfocus="sgBrwInput(this.value)" onblur="sgBrwBlur()">
          <input type="hidden" id="t-brw">
          <div id="brw-dd" class="sg-drop"></div>
        </div>
        <div id="brw-sel" class="sg-sel" style="display:none;">
          <i class="bi bi-person-check-fill" style="color:var(--success);flex-shrink:0;"></i>
          <span id="brw-sel-txt" style="flex:1;font-weight:500;"></span>
          <button class="sg-sel-x" onmousedown="sgBrwClear()"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>

      <div class="fgrp">
        <label class="flbl">Tanggal Pinjam *</label>
        <input type="datetime-local" id="t-ld" class="fc w-100">
      </div>

      <div class="fgrp">
        <label class="flbl">Tambah Barang ke Keranjang</label>
        <div class="d-flex gap-2 mb-2 align-items-start">
          <div class="sg-wrap" style="flex:1;min-width:0;position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none;z-index:1;"></i>
            <input type="text" id="t-inv-txt" class="fc" style="padding-left:36px;" placeholder="Cari kode / nama barang..." autocomplete="off" oninput="sgInvInput(this.value)" onfocus="sgInvInput(this.value)" onblur="sgInvBlur()">
            <div id="inv-dd" class="sg-drop"></div>
          </div>
          <button type="button" onclick="openScanner()" title="Scan QR/Barcode" style="flex-shrink:0;background:linear-gradient(135deg,#1E293B,#0F172A);border:none;color:#fff;padding:10px 14px;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;white-space:nowrap;box-shadow:0 2px 8px rgba(30,41,59,.2);transition:all .15s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''"><i class="bi bi-qr-code-scan" style="font-size:16px;"></i><span class="d-none d-sm-inline">Scan</span></button>
        </div>
        <div id="inv-sel" class="sg-sel" style="display:none;margin-bottom:8px;">
          <i class="bi bi-box-seam-fill" style="color:var(--accent);flex-shrink:0;"></i>
          <div style="flex:1;min-width:0;">
            <div id="inv-sel-nm" style="font-weight:500;font-size:13px;"></div>
            <div id="inv-sel-info" style="font-size:11px;color:var(--muted);"></div>
          </div>
          <button class="sg-sel-x" onmousedown="sgInvClear()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="d-flex gap-2">
          <input type="number" id="t-iq" class="fc" style="width:80px;flex-shrink:0;" min="1" value="1">
          <button class="b-pri w-100" onclick="addCart()"><i class="bi bi-plus-lg"></i> Tambah ke Keranjang</button>
        </div>
      </div>

      <div id="cart-box" style="margin-bottom:12px;"></div>

      <div class="fgrp">
        <label class="flbl">Foto Peminjam <span class="pw-req-badge">Wajib</span></label>
        <div id="pw-borrow"></div>
      </div>

      <div class="fgrp">
        <label class="flbl">Catatan</label>
        <textarea id="t-nt" class="fc w-100" rows="2" placeholder="Opsional..."></textarea>
      </div>

      <button class="b-acc w-100" onclick="submitTrx()"><i class="bi bi-check-circle"></i> Proses Transaksi</button>
    </div>
  </div>

  <!-- Right: Quick Info -->
  <div class="col-12 col-xl-7">
    <div class="row g-3">
      <div class="col-6 col-lg-3"><div class="stat-card sc-blue"><div class="stat-ico si-blue"><i class="bi bi-arrow-left-right"></i></div><div class="stat-val" id="qs-total">—</div><div class="stat-lbl">Total Transaksi</div></div></div>
      <div class="col-6 col-lg-3"><div class="stat-card sc-orange"><div class="stat-ico si-orange"><i class="bi bi-clock-fill"></i></div><div class="stat-val" id="qs-aktif">—</div><div class="stat-lbl">Aktif</div></div></div>
      <div class="col-6 col-lg-3"><div class="stat-card sc-green"><div class="stat-ico si-green"><i class="bi bi-check-circle-fill"></i></div><div class="stat-val" id="qs-done">—</div><div class="stat-lbl">Selesai</div></div></div>
      <div class="col-6 col-lg-3"><div class="stat-card sc-red"><div class="stat-ico si-red"><i class="bi bi-arrow-return-right"></i></div><div class="stat-val" id="qs-partial">—</div><div class="stat-lbl">Sebagian Kembali</div></div></div>
    </div>
    @if(auth()->user()->isAdmin())
    <!-- Pending approval section (admin only) -->
    <div id="pending-section" class="card p20 mt-3" style="display:none;border-left:3px solid var(--warning);">
      <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="st"><i class="bi bi-hourglass-split" style="color:var(--warning);"></i> Menunggu Persetujuan <span id="pending-cnt" class="badge ms-1" style="background:var(--warning);color:#1E293B;font-size:11px;"></span></div>
      </div>
      <div id="pending-list"></div>
    </div>
    @endif

    <div class="card p20 mt-3">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="st"><i class="bi bi-clock-history text-primary"></i> Transaksi Terbaru</div>
        <a href="{{ route('transactions.history') }}" class="b-out" style="font-size:12px;padding:6px 12px;"><i class="bi bi-list-ul"></i> Lihat Semua</a>
      </div>
      <div class="tw">
        <table class="table table-hover">
          <thead><tr><th>ID Trx</th><th>Peminjam</th><th>Tgl Pinjam</th><th style="text-align:center;">Item</th><th>Status</th></tr></thead>
          <tbody id="trx-tb"><tr><td colspan="5" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Scanner Modal -->
<div class="modal fade" id="mdl-scanner" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1E293B,#0F172A);color:#fff;border:none;padding:14px 20px;">
        <h5 class="modal-title" style="font-family:Poppins,sans-serif;font-weight:600;font-size:15px;display:flex;align-items:center;gap:8px;"><i class="bi bi-qr-code-scan"></i> Scan QR / Barcode</h5>
        <button type="button" class="btn-close btn-close-white" onclick="closeScanner()"></button>
      </div>
      <div class="modal-body" style="padding:0;background:#000;">
        <div style="position:relative;">
          <div id="qr-reader" style="width:100%;"></div>
          <div id="scan-overlay" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;">
            <div style="width:220px;height:220px;position:relative;">
              <div style="position:absolute;top:0;left:0;width:24px;height:24px;border-top:3px solid var(--accent);border-left:3px solid var(--accent);border-radius:4px 0 0 0;"></div>
              <div style="position:absolute;top:0;right:0;width:24px;height:24px;border-top:3px solid var(--accent);border-right:3px solid var(--accent);border-radius:0 4px 0 0;"></div>
              <div style="position:absolute;bottom:0;left:0;width:24px;height:24px;border-bottom:3px solid var(--accent);border-left:3px solid var(--accent);border-radius:0 0 0 4px;"></div>
              <div style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-bottom:3px solid var(--accent);border-right:3px solid var(--accent);border-radius:0 0 4px 0;"></div>
              <div id="scan-line" style="position:absolute;left:6px;right:6px;height:2px;background:var(--accent);box-shadow:0 0 8px var(--accent),0 0 16px rgba(249,115,22,.5);top:0;animation:scanAnim 2s ease-in-out infinite;"></div>
            </div>
          </div>
        </div>
        <div id="scanner-status" style="padding:12px 16px;text-align:center;font-size:12.5px;color:#fff;background:#1E293B;border-top:1px solid rgba(255,255,255,.1);">
          <span id="scan-status-txt"><i class="bi bi-hourglass-split me-1"></i>Menginisialisasi kamera...</span>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let _cart = [], _invT = [], _brwT = [], _selInv = null, _trxStore = [];

async function initTrx(){
  ld(true);
  try{
    const requests = [
      api('{{ route("inventory.data") }}'),
      api('{{ route("borrowers.data") }}'),
      api('{{ route("transactions.data") }}'),
    ];
    @if(auth()->user()->isAdmin())
    requests.push(api('{{ route("transactions.pending") }}'));
    @endif
    const results = await Promise.all(requests);
    ld(false);
    _invT = results[0].data || [];
    _brwT = results[1].data || [];
    _trxStore = results[2].data || [];
    const nonPending = _trxStore.filter(t=>t.status!=='menunggu_persetujuan'&&t.status!=='ditolak');
    const total   = nonPending.length;
    const aktif   = nonPending.filter(t=>t.status==='aktif').length;
    const done    = nonPending.filter(t=>t.status==='selesai').length;
    const partial = nonPending.filter(t=>t.status==='partial').length;
    document.getElementById('qs-total').textContent   = total;
    document.getElementById('qs-aktif').textContent   = aktif;
    document.getElementById('qs-done').textContent    = done;
    document.getElementById('qs-partial').textContent = partial;
    // Tampilkan semua transaksi (termasuk ditolak) kecuali yang masih menunggu persetujuan
    renderTrxRows(_trxStore.filter(t=>t.status!=='menunggu_persetujuan').slice(0,8));
    @if(auth()->user()->isAdmin())
    renderPending(results[3]?.data || []);
    @endif
    renderCart();
  }catch(e){ld(false);toast(e.message,'danger');}
  document.getElementById('t-ld').value = nowLocal();
  pwInit('borrow','pw-borrow','Foto Peminjam',true);
}

@if(auth()->user()->isAdmin())
function renderPending(list){
  const sec = document.getElementById('pending-section');
  const box = document.getElementById('pending-list');
  if(!list.length){sec.style.display='none';return;}
  sec.style.display='block';
  document.getElementById('pending-cnt').textContent = list.length;
  box.innerHTML = list.map(t=>{
    const items=(t.details||[]).map(d=>`<span class="bdg" style="background:rgba(100,116,139,.1);color:var(--muted);font-size:10px;">${esc(d.item_name)} ×${d.qty}</span>`).join(' ');
    return `<div style="border:1px solid var(--border);border-radius:10px;padding:13px;margin-bottom:10px;background:#fff;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
        <div>
          <code style="font-size:11px;color:var(--muted);">${esc(t.transaction_code)}</code>
          <div style="font-weight:700;font-size:14px;margin:2px 0;">${esc(t.borrower_name)}</div>
          <div style="font-size:11px;color:var(--muted);">${fdt(t.loan_date)} &bull; ${esc(t.created_by_name||'User')}</div>
        </div>
        <span class="bdg b-menunggu_persetujuan">Menunggu Konfirmasi</span>
      </div>
      <div style="margin-bottom:10px;display:flex;flex-wrap:wrap;gap:4px;">${items}</div>
      <div style="display:flex;gap:8px;">
        <button class="btn btn-sm btn-success" style="flex:1;font-weight:600;" onclick="approveTrx(${t.id})"><i class="bi bi-check-lg me-1"></i>Setujui</button>
        <button class="btn btn-sm btn-outline-danger" style="flex:1;font-weight:600;" onclick="rejectTrx(${t.id})"><i class="bi bi-x-lg me-1"></i>Tolak</button>
      </div>
    </div>`;
  }).join('');
}
async function approveTrx(id){
  if(!confirm('Setujui peminjaman ini? Stok barang akan dikurangi.'))return;
  try{const r=await api('/transactions/'+id+'/approve','POST');toast(r.message,'success');initTrx();}catch(e){toast(e.message,'danger');}
}
async function rejectTrx(id){
  if(!confirm('Tolak peminjaman ini?'))return;
  try{const r=await api('/transactions/'+id+'/reject','POST');toast(r.message,'success');initTrx();}catch(e){toast(e.message,'danger');}
}
@endif

function renderTrxRows(list){
  const tb = document.getElementById('trx-tb');
  if(!list.length){
    tb.innerHTML=`<tr><td colspan="5"><div class="empty"><div class="ei"><i class="bi bi-clipboard2-data"></i></div><p>Belum ada transaksi</p></div></td></tr>`;
    return;
  }
  // Bug fix: gunakan t.id (bukan index array) agar detail selalu tampil untuk transaksi yang benar
  tb.innerHTML = list.map(t=>`<tr class="tr-click" onclick="showTrxDetail(${t.id})" title="Klik untuk detail">
    <td><code style="font-size:10px;">${esc(t.transaction_code)}</code></td>
    <td>${esc(t.borrower_name)}</td>
    <td>${fdt(t.loan_date)}</td>
    <td style="text-align:center;">${t.details ? t.details.length : 0}</td>
    <td><span class="bdg b-${esc(t.status)}">${esc(statusLabel(t.status))}</span></td>
  </tr>`).join('');
}

function showTrxDetail(id){
  // Bug fix: cari berdasarkan ID transaksi, bukan index array
  const t = _trxStore.find(x=>x.id===id); if(!t) return;
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

// Borrower suggest
function sgBrwInput(q){
  const dd = document.getElementById('brw-dd'); if(!dd) return;
  const s = q.toLowerCase().trim();
  if(!s){dd.style.display='none';return;}
  const f = _brwT.filter(b=>b.name.toLowerCase().includes(s)||(b.department||'').toLowerCase().includes(s));
  if(!f.length){dd.innerHTML=`<div class="sg-item text-muted" style="cursor:default;">Tidak ada peminjam</div>`;dd.style.display='block';return;}
  dd.innerHTML = f.slice(0,8).map(b=>`<div class="sg-item" onmousedown="sgBrwSelect(${b.id},'${esc(b.name)}','${esc(b.department||'')}')"><div style="font-weight:500;">${esc(b.name)}</div>${b.department?`<div style="font-size:11px;color:var(--muted);">${esc(b.department)}</div>`:''}</div>`).join('');
  dd.style.display = 'block';
}
function sgBrwSelect(id,name,dept){
  document.getElementById('t-brw').value = id;
  const txt = document.getElementById('t-brw-txt'); txt.value=''; txt.placeholder = name+(dept?' — '+dept:'');
  document.getElementById('brw-dd').style.display='none';
  document.getElementById('brw-sel-txt').textContent = name+(dept?' — '+dept:'');
  document.getElementById('brw-sel').style.display='flex';
}
function sgBrwClear(){
  document.getElementById('t-brw').value='';
  const txt=document.getElementById('t-brw-txt');txt.value='';txt.placeholder='Ketik nama peminjam...';
  document.getElementById('brw-sel').style.display='none';
}
function sgBrwBlur(){setTimeout(()=>{const dd=document.getElementById('brw-dd');if(dd)dd.style.display='none';},180);}

// Inventory suggest
function sgInvInput(q){
  const dd=document.getElementById('inv-dd');if(!dd)return;
  const s=q.toLowerCase().trim();
  if(!s){dd.style.display='none';return;}
  const avail=_invT.filter(i=>i.available_qty>0);
  const f=avail.filter(i=>(i.name||'').toLowerCase().includes(s)||(i.code||'').toLowerCase().includes(s));
  if(!f.length){dd.innerHTML=`<div class="sg-item text-muted" style="cursor:default;">Barang tidak ditemukan</div>`;dd.style.display='block';return;}
  dd.innerHTML=f.slice(0,10).map(i=>`<div class="sg-item" onmousedown="sgInvSelect(${i.id},'${esc(i.name)}','${esc(i.code)}','${esc(i.type)}',${i.available_qty})">
    <div style="font-weight:500;">${esc(i.code)} — ${esc(i.name)}</div>
    <div style="font-size:11px;margin-top:2px;display:flex;align-items:center;gap:6px;"><span class="bdg ${i.type==='pinjam'?'b-pinjam':i.type==='bon'?'b-bon':'b-consumable'}" style="font-size:10px;">${i.type==='pinjam'?'Pinjam':i.type==='bon'?'BON':'Consumable'}</span><span style="color:var(--muted);">Stok: ${i.available_qty}</span></div>
  </div>`).join('');
  dd.style.display='block';
}
function sgInvSelect(id,name,code,type,av){
  _selInv={id,name,code,type,av};
  const txt=document.getElementById('t-inv-txt');txt.value='';txt.placeholder=code+' — '+name;
  document.getElementById('inv-dd').style.display='none';
  document.getElementById('inv-sel-nm').textContent=code+' — '+name;
  document.getElementById('inv-sel-info').innerHTML=`<i class="${type==='pinjam'?'bi bi-arrow-repeat text-info':type==='bon'?'bi bi-tag':'bi bi-fire text-danger'}"></i> `+(type==='pinjam'?'Pinjam':type==='bon'?'BON':'Consumable')+' | Stok: '+av;
  document.getElementById('inv-sel').style.display='flex';
}
function sgInvClear(){
  _selInv=null;
  const txt=document.getElementById('t-inv-txt');txt.value='';txt.placeholder='Ketik kode / nama barang...';
  document.getElementById('inv-sel').style.display='none';
}
function sgInvBlur(){setTimeout(()=>{const dd=document.getElementById('inv-dd');if(dd)dd.style.display='none';},200);}

// Cart
function addCart(){
  if(!_selInv){toast('Pilih barang dulu dari daftar pencarian!','warning');return;}
  const qty=parseInt(document.getElementById('t-iq').value)||0;
  if(qty<1){toast('Jumlah minimal 1!','warning');return;}
  const{id,name,code,type,av}=_selInv;
  const used=_cart.filter(c=>c.id===id).reduce((s,c)=>s+c.qty,0);
  if(used+qty>av){toast(`Stok tidak cukup! Sisa tersedia: ${av-used}`,'warning');return;}
  const ex=_cart.find(c=>c.id===id);
  if(ex)ex.qty+=qty;else _cart.push({id,name,code,type,qty,av});
  sgInvClear();document.getElementById('t-iq').value='1';renderCart();
}
function renderCart(){
  const box=document.getElementById('cart-box');if(!box)return;
  if(!_cart.length){box.innerHTML=`<div class="empty border rounded" style="padding:14px;background:var(--bg);border-style:dashed!important;"><i class="bi bi-cart3 fs-4 text-muted mb-2 d-block"></i><p class="mb-0">Keranjang kosong</p></div>`;return;}
  box.innerHTML=_cart.map((item,idx)=>`
    <div class="cart-item">
      <div class="c-dot" style="background:${item.type==='pinjam'?'var(--info)':item.type==='bon'?'#C2410C':'var(--accent)'};"></div>
      <div class="c-info">
        <div style="font-size:13px;font-weight:500;word-break:break-word;">${esc(item.name)}</div>
        <div style="font-size:11px;color:var(--muted);">${esc(item.code)} • <i class="${item.type==='pinjam'?'bi bi-arrow-repeat text-info':item.type==='bon'?'bi bi-tag':'bi bi-fire text-danger'}"></i> ${item.type==='pinjam'?'Pinjam':item.type==='bon'?'BON':'Consumable'}</div>
      </div>
      <div class="c-ctrl">
        <button class="c-btn" onclick="chCart(${idx},-1)">−</button>
        <span class="c-num">${item.qty}</span>
        <button class="c-btn" onclick="chCart(${idx},1)">+</button>
        <button class="c-btn ms-2" onclick="rmCart(${idx})" style="border-color:var(--danger);color:var(--danger);"><i class="bi bi-x"></i></button>
      </div>
    </div>`).join('');
}
function chCart(i,d){if(!_cart[i])return;_cart[i].qty=Math.max(1,Math.min(_cart[i].av,_cart[i].qty+d));renderCart();}
function rmCart(i){_cart.splice(i,1);renderCart();}


async function submitTrx(){
  const brwId=document.getElementById('t-brw').value;
  const loanDt=document.getElementById('t-ld').value;
  const notes=document.getElementById('t-nt').value.trim();
  if(!brwId){toast('Pilih peminjam terlebih dahulu!','warning');return;}
  if(!loanDt){toast('Tanggal pinjam wajib diisi!','warning');return;}
  if(!_cart.length){toast('Keranjang masih kosong!','warning');return;}
  if(!pwValidate('borrow','Foto Peminjam'))return;
  const brwNm=(_brwT.find(b=>b.id==brwId)||{}).name||'';
  const fd=new FormData();
  fd.append('borrower_id',brwId);
  fd.append('borrower_name',brwNm);
  fd.append('loan_date',loanDt);
  fd.append('notes',notes);
  fd.append('cart',JSON.stringify(_cart.map(c=>({inventory_id:c.id,qty:c.qty}))));
  fd.append('borrow_photo',getPhotoFile('borrow'));
  ld(true);
  try{
    const res=await apiForm('{{ route("transactions.store") }}',fd);
    ld(false);
    const toastType = res.pending ? 'warning' : 'success';
    toast(res.pending ? `${res.transaction_code} — Menunggu persetujuan admin.` : `Transaksi ${res.transaction_code} berhasil!`, toastType);
    _cart=[];_selInv=null;
    document.getElementById('t-brw').value='';
    document.getElementById('t-brw-txt').value='';
    document.getElementById('t-brw-txt').placeholder='Ketik nama peminjam...';
    document.getElementById('brw-sel').style.display='none';
    document.getElementById('t-nt').value='';
    _pwClear('borrow');renderCart();
    const trxR=await api('{{ route("transactions.data") }}');
    _trxStore=trxR.data||[];
    const nonP=_trxStore.filter(t=>t.status!=='menunggu_persetujuan'&&t.status!=='ditolak');
    document.getElementById('qs-total').textContent  =nonP.length;
    document.getElementById('qs-aktif').textContent  =nonP.filter(t=>t.status==='aktif').length;
    document.getElementById('qs-done').textContent   =nonP.filter(t=>t.status==='selesai').length;
    document.getElementById('qs-partial').textContent=nonP.filter(t=>t.status==='partial').length;
    // Tampilkan semua (termasuk ditolak) kecuali menunggu persetujuan
    renderTrxRows(_trxStore.filter(t=>t.status!=='menunggu_persetujuan').slice(0,8));
    @if(auth()->user()->isAdmin())
    const pendR=await api('{{ route("transactions.pending") }}');
    renderPending(pendR.data||[]);
    @endif
  }catch(e){ld(false);toast(e.message,'danger');}
}

initTrx();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" crossorigin="anonymous"></script>
<style>
@keyframes scanAnim{0%{top:6px;}50%{top:calc(100% - 8px);}100%{top:6px;}}
#qr-reader video{width:100%!important;display:block;object-fit:cover;}
#qr-reader{min-height:300px;background:#000;}
#qr-reader img[alt="Info icon"]{display:none!important;}
#qr-reader__scan_region{background:transparent!important;border:none!important;}
#qr-reader__header_message{display:none!important;}
#qr-reader__status_span{display:none!important;}
#qr-reader select{display:none!important;}
#qr-reader__camera_selection{display:none!important;}
#qr-reader__dashboard_section_csr button{display:none!important;}
#qr-reader__dashboard{display:none!important;}
</style>
<script>
let _qrScanner = null;
let _scannerModal = null;

function openScanner(){
  if(typeof Html5Qrcode === 'undefined'){
    toast('Library scanner tidak tersedia. Coba refresh halaman.','danger');
    return;
  }
  document.getElementById('scan-status-txt').innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menginisialisasi kamera...';
  _scannerModal = new bootstrap.Modal(document.getElementById('mdl-scanner'));
  _scannerModal.show();
  document.getElementById('mdl-scanner').addEventListener('shown.bs.modal', _startScanner, {once:true});
}

function _startScanner(){
  const config = {fps:10, qrbox:{width:230, height:230}, aspectRatio:1.0, rememberLastUsedCamera:true};
  _qrScanner = new Html5Qrcode('qr-reader');
  Html5Qrcode.getCameras().then(cameras => {
    if(!cameras || !cameras.length){
      document.getElementById('scan-status-txt').innerHTML = '<i class="bi bi-exclamation-triangle text-warning me-1"></i>Tidak ada kamera ditemukan';
      return;
    }
    const virtualRe = /obs|virtual|screen|capture|dshow|snap|zoom|teams|skype|manycam|wirecast|xsplit/i;
    const physical = cameras.filter(c => !virtualRe.test(c.label));
    const pool = physical.length ? physical : cameras;
    const selected = pool.find(c => /back|rear|environment/i.test(c.label))
                  || pool.find(c => /integrated|built.?in|facetime|webcam|hd cam|usb cam|laptop/i.test(c.label))
                  || pool[0];
    _qrScanner.start(selected.id, config, _onScanSuccess, ()=>{}).then(()=>{
      document.getElementById('scan-status-txt').innerHTML = '<i class="bi bi-camera-fill text-success me-1"></i>Arahkan kamera ke QR / barcode barang...';
    }).catch(()=>{
      document.getElementById('scan-status-txt').innerHTML = '<i class="bi bi-exclamation-circle text-danger me-1"></i>Izin kamera diperlukan. Izinkan akses kamera di browser.';
    });
  }).catch(()=>{
    document.getElementById('scan-status-txt').innerHTML = '<i class="bi bi-exclamation-circle text-danger me-1"></i>Izin kamera ditolak. Periksa pengaturan browser.';
  });
}

function _onScanSuccess(decodedText){
  const code = decodedText.trim();
  closeScanner();
  const exact = _invT.find(i => i.code === code);
  if(exact){
    if(exact.available_qty > 0){
      sgInvSelect(exact.id, exact.name, exact.code, exact.type, exact.available_qty);
      toast('Barang berhasil dipindai: '+exact.code+' — '+exact.name, 'success');
    } else {
      toast('Barang "'+exact.code+'" ditemukan tapi stok habis!','warning');
      document.getElementById('t-inv-txt').value = code;
      sgInvInput(code);
    }
  } else {
    document.getElementById('t-inv-txt').value = code;
    sgInvInput(code);
    const partial = _invT.filter(i => (i.code||'').toLowerCase().includes(code.toLowerCase()) || (i.name||'').toLowerCase().includes(code.toLowerCase()));
    if(!partial.length){
      toast('Barang dengan kode "'+code+'" tidak ditemukan','warning');
    } else {
      toast('Kode dipindai: '+code+' — Pilih dari daftar','info');
    }
  }
}

function closeScanner(){
  if(_qrScanner){
    _qrScanner.isScanning ? _qrScanner.stop().then(()=>{_qrScanner.clear();_qrScanner=null;}).catch(()=>{_qrScanner=null;}) : (_qrScanner.clear(), _qrScanner=null);
  }
  if(_scannerModal){_scannerModal.hide();_scannerModal=null;}
}

document.getElementById('mdl-scanner').addEventListener('hide.bs.modal',()=>{
  if(_qrScanner && _qrScanner.isScanning){_qrScanner.stop().catch(()=>{});_qrScanner=null;}
});
</script>
@endpush
