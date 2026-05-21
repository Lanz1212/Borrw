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
        <div class="sg-wrap sw mb-2">
          <i class="bi bi-search"></i>
          <input type="text" id="t-inv-txt" class="fc" placeholder="Cari kode / nama barang..." autocomplete="off" oninput="sgInvInput(this.value)" onfocus="sgInvInput(this.value)" onblur="sgInvBlur()">
          <div id="inv-dd" class="sg-drop"></div>
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
        <label class="flbl">Tanda Tangan <small style="color:var(--muted);font-size:11px;">(gambar di kotak)</small></label>
        <div style="width:100%;border:2px dashed var(--border);border-radius:10px;background:#fff;overflow:hidden;">
          <canvas id="sig-cv" height="110" style="width:100%;display:block;touch-action:none;"></canvas>
        </div>
        <button class="b-out mt-2" style="font-size:12px;padding:6px 12px;" onclick="clrSig()"><i class="bi bi-eraser me-1"></i>Hapus TTD</button>
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
      <div class="col-12 col-sm-4"><div class="stat-card sc-blue"><div class="stat-ico si-blue"><i class="bi bi-arrow-left-right"></i></div><div class="stat-val" id="qs-total">—</div><div class="stat-lbl">Total Transaksi</div></div></div>
      <div class="col-12 col-sm-4"><div class="stat-card sc-orange"><div class="stat-ico si-orange"><i class="bi bi-clock-fill"></i></div><div class="stat-val" id="qs-aktif">—</div><div class="stat-lbl">Sedang Aktif</div></div></div>
      <div class="col-12 col-sm-4"><div class="stat-card sc-green"><div class="stat-ico si-green"><i class="bi bi-check-circle-fill"></i></div><div class="stat-val" id="qs-done">—</div><div class="stat-lbl">Selesai</div></div></div>
    </div>
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
<!-- Transaction Detail Modal -->
<div class="modal fade" id="mdl-trx-detail" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="bi bi-clipboard2-data"></i> Detail Transaksi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mdl-trx-body"></div>
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
    const [invR, brwR, trxR] = await Promise.all([
      api('{{ route("inventory.data") }}'),
      api('{{ route("borrowers.data") }}'),
      api('{{ route("transactions.data") }}'),
    ]);
    ld(false);
    _invT = invR.data || [];
    _brwT = brwR.data || [];
    _trxStore = trxR.data || [];
    const total = _trxStore.length;
    const aktif = _trxStore.filter(t=>t.status==='aktif'||t.status==='partial').length;
    const done  = _trxStore.filter(t=>t.status==='selesai').length;
    document.getElementById('qs-total').textContent = total;
    document.getElementById('qs-aktif').textContent = aktif;
    document.getElementById('qs-done').textContent = done;
    renderTrxRows(_trxStore.slice(0,8));
    renderCart();
  }catch(e){ld(false);toast(e.message,'danger');}
  document.getElementById('t-ld').value = nowLocal();
  setTimeout(initSig, 100);
}

function renderTrxRows(list){
  const tb = document.getElementById('trx-tb');
  if(!list.length){
    tb.innerHTML=`<tr><td colspan="5"><div class="empty"><div class="ei"><i class="bi bi-clipboard2-data"></i></div><p>Belum ada transaksi</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = list.map((t,idx)=>`<tr class="tr-click" onclick="showTrxDetail(${idx})" title="Klik untuk detail">
    <td><code style="font-size:10px;">${esc(t.transaction_code)}</code></td>
    <td>${esc(t.borrower_name)}</td>
    <td>${fdt(t.loan_date)}</td>
    <td style="text-align:center;">${t.details ? t.details.length : 0}</td>
    <td><span class="bdg b-${esc(t.status)}">${esc(statusLabel(t.status))}</span></td>
  </tr>`).join('');
}

function showTrxDetail(idx){
  const t = _trxStore[idx]; if(!t) return;
  const detailRows = (t.details||[]).map(d=>`<tr>
    <td style="font-weight:500;white-space:normal;min-width:140px;">${esc(d.item_name)}</td>
    <td><code style="font-size:10px;">${esc(d.item_code)}</code></td>
    <td><span class="bdg ${d.item_type==='pinjam'?'b-pinjam':'b-consumable'}"><i class="${d.item_type==='pinjam'?'bi bi-arrow-repeat':'bi bi-fire'}"></i> ${d.item_type==='pinjam'?'Pinjam':'Consumable'}</span></td>
    <td style="text-align:center;">${d.qty}</td>
    <td style="text-align:center;">${d.qty_returned||0}</td>
    <td><span class="bdg b-${esc(d.status)}">${esc(statusLabel(d.status))}</span></td>
  </tr>`).join('') || `<tr><td colspan="6" class="text-center" style="color:var(--muted);padding:16px;">Tidak ada item</td></tr>`;

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
    ${t.signature?`<div style="margin-top:16px;"><div class="dg-lbl" style="font-size:11px;font-weight:600;margin-bottom:6px;">TANDA TANGAN PEMINJAM</div><div id="sig-box" style="border:1.5px solid var(--border);border-radius:8px;padding:8px;display:inline-block;background:#fff;min-height:36px;"></div></div>`:''}
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;margin-top:20px;"><i class="bi bi-box-seam text-primary"></i> Daftar Barang</div>
    <div class="tw"><table class="table">
      <thead><tr><th>Nama Barang</th><th>Kode</th><th>Jenis</th><th style="text-align:center;">Jml</th><th style="text-align:center;">Kembali</th><th>Status</th></tr></thead>
      <tbody>${detailRows}</tbody>
    </table></div>`;
  if(t.signature) renderSig(t.signature, document.getElementById('sig-box'));
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
    <div style="font-size:11px;margin-top:2px;display:flex;align-items:center;gap:6px;"><span class="bdg ${i.type==='pinjam'?'b-pinjam':'b-consumable'}" style="font-size:10px;">${i.type==='pinjam'?'Pinjam':'Consumable'}</span><span style="color:var(--muted);">Stok: ${i.available_qty}</span></div>
  </div>`).join('');
  dd.style.display='block';
}
function sgInvSelect(id,name,code,type,av){
  _selInv={id,name,code,type,av};
  const txt=document.getElementById('t-inv-txt');txt.value='';txt.placeholder=code+' — '+name;
  document.getElementById('inv-dd').style.display='none';
  document.getElementById('inv-sel-nm').textContent=code+' — '+name;
  document.getElementById('inv-sel-info').innerHTML=`<i class="${type==='pinjam'?'bi bi-arrow-repeat text-info':'bi bi-fire text-danger'}"></i> `+(type==='pinjam'?'Pinjam':'Consumable')+' | Stok: '+av;
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
      <div class="c-dot" style="background:${item.type==='pinjam'?'var(--info)':'var(--accent)'};"></div>
      <div class="c-info">
        <div style="font-size:13px;font-weight:500;word-break:break-word;">${esc(item.name)}</div>
        <div style="font-size:11px;color:var(--muted);">${esc(item.code)} • ${item.type==='pinjam'?'🔄 Pinjam':'🔥 Consumable'}</div>
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

// Signature
function initSig(){
  const cv=document.getElementById('sig-cv');if(!cv)return;
  const rect=cv.parentElement.getBoundingClientRect();cv.width=rect.width;
  const ctx=cv.getContext('2d');let drw=false,lx=0,ly=0;
  function pos(e){const r=cv.getBoundingClientRect(),s=e.touches?e.touches[0]:e;return{x:s.clientX-r.left,y:s.clientY-r.top};}
  function start(e){e.preventDefault();drw=true;const p=pos(e);lx=p.x;ly=p.y;}
  function move(e){if(!drw)return;e.preventDefault();const p=pos(e);ctx.beginPath();ctx.moveTo(lx,ly);ctx.lineTo(p.x,p.y);ctx.strokeStyle='#000000';ctx.lineWidth=2;ctx.lineCap='round';ctx.stroke();lx=p.x;ly=p.y;}
  function stop(){drw=false;}
  cv.addEventListener('mousedown',start);cv.addEventListener('touchstart',start,{passive:false});
  cv.addEventListener('mousemove',move);cv.addEventListener('touchmove',move,{passive:false});
  cv.addEventListener('mouseup',stop);cv.addEventListener('touchend',stop);
}
function clrSig(){const c=document.getElementById('sig-cv');if(c)c.getContext('2d').clearRect(0,0,c.width,c.height);}

async function submitTrx(){
  const brwId=document.getElementById('t-brw').value;
  const loanDt=document.getElementById('t-ld').value;
  const notes=document.getElementById('t-nt').value.trim();
  if(!brwId){toast('Pilih peminjam terlebih dahulu!','warning');return;}
  if(!loanDt){toast('Tanggal pinjam wajib diisi!','warning');return;}
  if(!_cart.length){toast('Keranjang masih kosong!','warning');return;}
  const brwNm=(_brwT.find(b=>b.id==brwId)||{}).name||'';
  let sig='';
  const cv=document.getElementById('sig-cv');
  if(cv){try{const tmp=document.createElement('canvas');tmp.width=180;tmp.height=50;const tc=tmp.getContext('2d');tc.fillStyle='#ffffff';tc.fillRect(0,0,180,50);tc.drawImage(cv,0,0,180,50);sig=tmp.toDataURL('image/jpeg',0.7);}catch(e){sig='';}}
  const txData={
    borrower_id:parseInt(brwId),borrower_name:brwNm,
    loan_date:new Date(loanDt).toISOString(),
    notes,signature:sig,
    cart:_cart.map(c=>({inventory_id:c.id,qty:c.qty}))
  };
  ld(true);
  try{
    const res=await api('{{ route("transactions.store") }}','POST',txData);
    ld(false);
    toast(`Transaksi ${res.transaction_code} berhasil!`,'success');
    _cart=[];_selInv=null;
    document.getElementById('t-brw').value='';
    document.getElementById('t-brw-txt').value='';
    document.getElementById('t-brw-txt').placeholder='Ketik nama peminjam...';
    document.getElementById('brw-sel').style.display='none';
    document.getElementById('t-nt').value='';
    clrSig();renderCart();
    const trxR=await api('{{ route("transactions.data") }}');
    _trxStore=trxR.data||[];
    const total=_trxStore.length, aktif=_trxStore.filter(t=>t.status==='aktif'||t.status==='partial').length, done=_trxStore.filter(t=>t.status==='selesai').length;
    document.getElementById('qs-total').textContent=total;document.getElementById('qs-aktif').textContent=aktif;document.getElementById('qs-done').textContent=done;
    renderTrxRows(_trxStore.slice(0,8));
  }catch(e){ld(false);toast(e.message,'danger');}
}

initTrx();
</script>
@endpush
