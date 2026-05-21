@extends('layouts.app')
@section('title','Pengembalian Barang')
@section('page-title','Pengembalian Barang')

@section('content')
<div class="row g-3">
  <!-- Left: Process Return -->
  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-arrow-return-left text-primary"></i> Proses Pengembalian</div>

      <div class="fgrp">
        <label class="flbl">Cari Transaksi Aktif * <small class="text-muted">(Ketik nama / ID)</small></label>
        <div class="sg-wrap w-100">
          <input type="text" id="r-sel-txt" class="fc w-100" placeholder="Ketik nama peminjam atau ID..." autocomplete="off"
            oninput="sgRetInput(this.value)" onfocus="sgRetInput(this.value)" onblur="sgRetBlur()">
          <input type="hidden" id="r-sel">
          <div id="r-dd" class="sg-drop"></div>
        </div>
        <div id="ret-sel-ui" class="sg-sel" style="display:none;">
          <i class="bi bi-check-circle-fill" style="color:var(--success);flex-shrink:0;"></i>
          <span id="ret-sel-lbl" style="flex:1;font-weight:500;"></span>
          <button class="sg-sel-x" onmousedown="sgRetClear()"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>

      <div id="r-items"><div class="empty"><div class="ei"><i class="bi bi-box-seam"></i></div><p>Cari transaksi untuk lanjut</p></div></div>
      <button id="r-btn" class="b-acc w-100" style="display:none;margin-top:12px;" onclick="submitRet()"><i class="bi bi-check-circle"></i> Proses Pengembalian</button>
    </div>
  </div>

  <!-- Right: Transaction Detail -->
  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-card-list text-primary"></i> Detail Transaksi</div>
      <div id="r-info"><div class="empty"><div class="ei"><i class="bi bi-card-list"></i></div><p>Pilih transaksi</p></div></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let _retL = [], _retT = null, _retP = [];

async function initRet(){
  ld(true);
  try{
    const res = await api('{{ route("transactions.active") }}');
    ld(false);
    _retL = res.data || [];
  }catch(e){ld(false);toast(e.message,'danger');}
}

function sgRetInput(q){
  const dd=document.getElementById('r-dd');if(!dd)return;
  const s=q.toLowerCase().trim();
  if(!s){dd.style.display='none';return;}
  const f=_retL.filter(t=>(t.transaction_code||'').toLowerCase().includes(s)||(t.borrower_name||'').toLowerCase().includes(s));
  if(!f.length){dd.innerHTML=`<div class="sg-item text-muted" style="cursor:default;">Tidak ada transaksi aktif</div>`;dd.style.display='block';return;}
  dd.innerHTML=f.slice(0,8).map(t=>`<div class="sg-item" onmousedown="sgRetSelect(${t.id},'${esc(t.transaction_code)} — ${esc(t.borrower_name)}')">
    <div style="font-weight:500;">${esc(t.borrower_name)}</div>
    <div style="font-size:11px;color:var(--muted);">${esc(t.transaction_code)} • ${fdt(t.loan_date)}</div>
  </div>`).join('');
  dd.style.display='block';
}
function sgRetSelect(id, lbl){
  document.getElementById('r-sel').value=id;
  const txt=document.getElementById('r-sel-txt');txt.value='';txt.placeholder=lbl;
  document.getElementById('r-dd').style.display='none';
  document.getElementById('ret-sel-lbl').textContent=lbl;
  document.getElementById('ret-sel-ui').style.display='flex';
  loadRetItems();
}
function sgRetClear(){
  document.getElementById('r-sel').value='';
  const txt=document.getElementById('r-sel-txt');txt.value='';txt.placeholder='Ketik nama peminjam atau ID...';
  document.getElementById('ret-sel-ui').style.display='none';
  _retT=null;_retP=[];
  document.getElementById('r-items').innerHTML=`<div class="empty"><div class="ei"><i class="bi bi-box-seam"></i></div><p>Cari transaksi untuk lanjut</p></div>`;
  document.getElementById('r-info').innerHTML=`<div class="empty"><div class="ei"><i class="bi bi-card-list"></i></div><p>Pilih transaksi</p></div>`;
  document.getElementById('r-btn').style.display='none';
}
function sgRetBlur(){setTimeout(()=>{const dd=document.getElementById('r-dd');if(dd)dd.style.display='none';},200);}

function loadRetItems(){
  const id=parseInt(document.getElementById('r-sel').value);
  const trx=_retL.find(t=>t.id===id);
  if(!trx){sgRetClear();return;}
  _retT=trx;

  document.getElementById('r-info').innerHTML=`
    <div style="font-size:13px;line-height:2.2;">
      <div><strong>ID:</strong> <code style="word-break:break-all;">${esc(trx.transaction_code)}</code></div>
      <div><strong>Peminjam:</strong> ${esc(trx.borrower_name)}</div>
      <div><strong>Tgl Pinjam:</strong> ${fdt(trx.loan_date)}</div>
      <div><strong>Status:</strong> <span class="bdg b-${esc(trx.status)}">${esc(statusLabel(trx.status))}</span></div>
    </div>
    <hr style="margin:16px 0;">
    <div style="font-size:13px;font-weight:600;margin-bottom:10px;"><i class="bi bi-box-seam text-primary"></i> Item Dipinjam:</div>
    ${(trx.details||[]).map(d=>`<div style="padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;display:flex;justify-content:space-between;align-items:center;">
      <span style="word-break:break-word;" class="pe-2"><strong>${esc(d.item_name)}</strong> × ${d.qty}</span>
      <span class="bdg b-${esc(d.status)} flex-shrink-0">${esc(statusLabel(d.status))}</span>
    </div>`).join('')}`;

  const pending=(trx.details||[]).filter(d=>d.status==='dipinjam');
  _retP=pending;

  if(!pending.length){
    document.getElementById('r-items').innerHTML=`<p style="color:var(--success);font-size:13px;text-align:center;padding:14px;"><i class="bi bi-check-circle-fill"></i> Semua item sudah dikembalikan.</p>`;
    document.getElementById('r-btn').style.display='none';return;
  }

  const selHdr=`<div style="font-size:12px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;"><span style="color:var(--muted);font-weight:500;">Pilih barang yang akan dikembalikan:</span><span style="display:flex;gap:10px;"><a href="#" onclick="toggleAllRet(true);return false;" style="color:var(--accent);text-decoration:none;font-size:11.5px;font-weight:600;">Pilih Semua</a><a href="#" onclick="toggleAllRet(false);return false;" style="color:var(--muted);text-decoration:none;font-size:11.5px;">Batal Semua</a></span></div>`;
  document.getElementById('r-items').innerHTML=selHdr+pending.map(d=>{
    const rem=d.qty-(d.qty_returned||0);
    return`<div class="card p16 mb-2" id="ri-${d.id}" style="border:1px solid var(--border);transition:opacity .15s;">
      <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;margin-bottom:10px;margin-right:0;">
        <input type="checkbox" class="ret-chk" id="rc-${d.id}" checked style="width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:var(--accent);cursor:pointer;" onchange="onRetChk(${d.id})">
        <div style="flex:1;min-width:0;">
          <div style="font-weight:600;font-size:13px;word-break:break-word;">${esc(d.item_name)}</div>
          <div style="font-size:11px;color:var(--muted);">${esc(d.item_code)} • Sisa pinjam: ${rem} unit</div>
        </div>
      </label>
      <div id="rf-${d.id}" class="row g-2">
        <div class="col-12 col-sm-4"><label class="flbl" style="font-size:11px;">Jml Kembali</label><input type="number" class="fc fc-sm w-100" id="rq-${d.id}" min="0" max="${rem}" value="${rem}"></div>
        <div class="col-6 col-sm-4"><label class="flbl" style="font-size:11px;">Jml Rusak</label><input type="number" class="fc fc-sm w-100" id="rd-${d.id}" min="0" value="0"></div>
        <div class="col-6 col-sm-4"><label class="flbl" style="font-size:11px;">Jml Hilang</label><input type="number" class="fc fc-sm w-100" id="rl-${d.id}" min="0" value="0"></div>
        <div class="col-12"><label class="flbl" style="font-size:11px;">Catatan</label><input type="text" class="fc fc-sm w-100" id="rn-${d.id}" placeholder="Keterangan kondisi..."></div>
      </div>
    </div>`;
  }).join('');
  document.getElementById('r-btn').style.display='flex';
  updateRetBtn();
}

async function submitRet(){
  const trx=_retT,pending=_retP||[];
  if(!trx||!pending.length){toast('Tidak ada item untuk dikembalikan.','warning');return;}
  const items=[];
  for(const d of pending){
    const chk=document.getElementById(`rc-${d.id}`);
    if(!chk?.checked) continue;
    const qr=parseInt(document.getElementById(`rq-${d.id}`)?.value)||0;
    const qd=parseInt(document.getElementById(`rd-${d.id}`)?.value)||0;
    const ql=parseInt(document.getElementById(`rl-${d.id}`)?.value)||0;
    const nt=document.getElementById(`rn-${d.id}`)?.value||'';
    if(qr<1){toast(`Jumlah kembali "${d.item_name}" minimal 1!`,'warning');return;}
    if(qd+ql>qr){toast(`Rusak+hilang tidak boleh melebihi jumlah kembali untuk "${d.item_name}"!`,'warning');return;}
    items.push({detail_id:d.id,qty_returned:qr,qty_damaged:qd,qty_lost:ql,notes:nt});
  }
  if(!items.length){toast('Pilih minimal satu item untuk dikembalikan.','warning');return;}
  ld(true);
  try{
    const res=await api('{{ route("returns.store") }}','POST',{transaction_id:trx.id,items});
    ld(false);toast(res.message,'success');
    sgRetClear();
    const r=await api('{{ route("transactions.active") }}');
    _retL=r.data||[];
  }catch(e){ld(false);toast(e.message,'danger');}
}

function onRetChk(id){
  const chk=document.getElementById(`rc-${id}`);
  const fields=document.getElementById(`rf-${id}`);
  const card=document.getElementById(`ri-${id}`);
  if(chk.checked){
    fields.style.opacity='1';fields.style.pointerEvents='auto';
    card.style.opacity='1';card.style.borderColor='var(--border)';
  } else {
    fields.style.opacity='0.35';fields.style.pointerEvents='none';
    card.style.opacity='0.55';card.style.borderColor='rgba(100,116,139,.2)';
  }
  updateRetBtn();
}

function updateRetBtn(){
  const cnt=document.querySelectorAll('.ret-chk:checked').length;
  const btn=document.getElementById('r-btn');
  if(!btn)return;
  if(cnt>0){
    btn.innerHTML=`<i class="bi bi-check-circle"></i> Proses Pengembalian (${cnt} item dipilih)`;
    btn.disabled=false;btn.style.opacity='1';btn.style.cursor='pointer';
  } else {
    btn.innerHTML=`<i class="bi bi-check-circle"></i> Pilih minimal 1 item`;
    btn.disabled=true;btn.style.opacity='0.55';btn.style.cursor='not-allowed';
  }
}

function toggleAllRet(checked){
  document.querySelectorAll('.ret-chk').forEach(chk=>{
    if(chk.checked!==checked){
      chk.checked=checked;
      onRetChk(parseInt(chk.id.replace('rc-','')));
    }
  });
}

initRet();
</script>
@endpush
