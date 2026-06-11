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

<!-- Scanner Modal -->
<div class="modal fade" id="mdl-dmg-scanner" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1E293B,#0F172A);color:#fff;border:none;padding:14px 20px;">
        <h5 class="modal-title" style="font-weight:600;font-size:15px;display:flex;align-items:center;gap:8px;"><i class="bi bi-qr-code-scan"></i> Scan QR / Barcode</h5>
        <button type="button" class="btn-close btn-close-white" onclick="closeDmgScanner()"></button>
      </div>
      <div class="modal-body" style="padding:0;background:#000;">
        <div style="position:relative;">
          <div id="dmg-qr-reader" style="width:100%;"></div>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;">
            <div style="width:220px;height:220px;position:relative;">
              <div style="position:absolute;top:0;left:0;width:24px;height:24px;border-top:3px solid var(--accent);border-left:3px solid var(--accent);border-radius:4px 0 0 0;"></div>
              <div style="position:absolute;top:0;right:0;width:24px;height:24px;border-top:3px solid var(--accent);border-right:3px solid var(--accent);border-radius:0 4px 0 0;"></div>
              <div style="position:absolute;bottom:0;left:0;width:24px;height:24px;border-bottom:3px solid var(--accent);border-left:3px solid var(--accent);border-radius:0 0 0 4px;"></div>
              <div style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-bottom:3px solid var(--accent);border-right:3px solid var(--accent);border-radius:0 0 4px 0;"></div>
              <div style="position:absolute;left:6px;right:6px;height:2px;background:var(--accent);box-shadow:0 0 8px var(--accent),0 0 16px rgba(249,115,22,.5);top:0;animation:scanAnim 2s ease-in-out infinite;"></div>
            </div>
          </div>
        </div>
        <div style="padding:12px 16px;text-align:center;font-size:12.5px;color:#fff;background:#1E293B;border-top:1px solid rgba(255,255,255,.1);">
          <span id="dmg-scan-status-txt"><i class="bi bi-hourglass-split me-1"></i>Menginisialisasi kamera...</span>
        </div>
      </div>
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
        <div class="d-flex gap-2 mb-2 align-items-start">
          <div class="sg-wrap" style="flex:1;min-width:0;position:relative;">
            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none;z-index:1;"></i>
            <input type="text" id="d-inv-txt" class="fc" style="padding-left:36px;" placeholder="Cari kode / nama barang..." autocomplete="off" oninput="sgDmgInvInput(this.value)" onfocus="sgDmgInvInput(this.value)" onblur="sgDmgInvBlur()">
            <div id="d-inv-dd" class="sg-drop"></div>
          </div>
          <button type="button" onclick="openDmgScanner()" title="Scan QR/Barcode" style="flex-shrink:0;background:linear-gradient(135deg,#1E293B,#0F172A);border:none;color:#fff;padding:10px 14px;border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;white-space:nowrap;box-shadow:0 2px 8px rgba(30,41,59,.2);transition:all .15s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''"><i class="bi bi-qr-code-scan" style="font-size:16px;"></i><span class="d-none d-sm-inline">Scan</span></button>
        </div>
        <input type="hidden" id="d-item">
        <div id="d-inv-sel" class="sg-sel" style="display:none;margin-bottom:4px;">
          <i class="bi bi-box-seam-fill" style="color:var(--accent);flex-shrink:0;"></i>
          <div style="flex:1;min-width:0;">
            <div id="d-inv-sel-nm" style="font-weight:500;font-size:13px;"></div>
            <div id="d-inv-sel-info" style="font-size:11px;color:var(--muted);"></div>
          </div>
          <button class="sg-sel-x" onmousedown="sgDmgInvClear()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="d-inv-info" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-top:2px;">
          <div style="display:flex;align-items:flex-start;gap:10px;">
            <i class="bi bi-box-seam-fill" style="color:#16a34a;font-size:20px;flex-shrink:0;margin-top:2px;"></i>
            <div style="flex:1;min-width:0;">
              <div id="d-info-name" style="font-weight:700;font-size:14px;word-break:break-word;"></div>
              <div id="d-info-meta" style="font-size:11px;color:var(--muted);margin-top:2px;"></div>
              <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap;">
                <div style="font-size:12px;"><span style="color:var(--muted);">Stok tersedia:</span> <strong id="d-info-stock" style="color:#16a34a;"></strong></div>
                <div style="font-size:12px;"><span style="color:var(--muted);">Terakhir rusak:</span> <strong id="d-info-lastdmg"></strong></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="fgrp">
        <label class="flbl">Jumlah Rusak *</label>
        <input type="number" id="d-qty" class="fc w-100" min="1" value="1">
      </div>
      <div class="fgrp">
        <label class="flbl">Keterangan *</label>
        <textarea id="d-desc" class="fc w-100" rows="3" placeholder="Penyebab kerusakan..."></textarea>
      </div>
      <div class="fgrp">
        <label class="flbl">Foto Kerusakan <span class="pw-req-badge">Wajib</span></label>
        <div id="pw-dmg-manual"></div>
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
<script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
<style>
@keyframes scanAnim{0%{top:6px;}50%{top:calc(100% - 8px);}100%{top:6px;}}
#dmg-qr-reader video{width:100%!important;display:block;object-fit:cover;}
#dmg-qr-reader{min-height:300px;background:#000;}
#dmg-qr-reader img[alt="Info icon"]{display:none!important;}
#dmg-qr-reader__scan_region{background:transparent!important;border:none!important;}
#dmg-qr-reader__header_message{display:none!important;}
#dmg-qr-reader__status_span{display:none!important;}
#dmg-qr-reader select{display:none!important;}
#dmg-qr-reader__camera_selection{display:none!important;}
#dmg-qr-reader__dashboard_section_csr button{display:none!important;}
#dmg-qr-reader__dashboard{display:none!important;}
</style>
<script>
let _dmgStore = [], _dmgInvT = [], _selDmgInv = null;
let _dmgQrScanner = null, _dmgScannerModal = null;

async function initDmg(){await loadDmg();pwInit('dmg_manual','pw-dmg-manual','Foto Kerusakan',true);}
async function loadDmg(){
  ld(true);
  try{
    const [dmgR, invR] = await Promise.all([
      api('{{ route("damaged.data") }}'),
      api('{{ route("inventory.data") }}'),
    ]);
    ld(false);
    _dmgStore = dmgR.data || [];
    _dmgInvT  = invR.data || [];
    renderDmgRows(_dmgStore);
  }catch(e){ld(false);toast(e.message,'danger');}
}

function sgDmgInvInput(q){
  const dd=document.getElementById('d-inv-dd');if(!dd)return;
  const s=q.toLowerCase().trim();
  if(!s){dd.style.display='none';return;}
  const f=_dmgInvT.filter(i=>(i.name||'').toLowerCase().includes(s)||(i.code||'').toLowerCase().includes(s));
  if(!f.length){dd.innerHTML=`<div class="sg-item text-muted" style="cursor:default;">Barang tidak ditemukan</div>`;dd.style.display='block';return;}
  dd.innerHTML=f.slice(0,10).map(i=>`<div class="sg-item" onmousedown="sgDmgInvSelect(${i.id},'${esc(i.name)}','${esc(i.code)}','${esc(i.type)}',${i.total_qty})">
    <div style="font-weight:500;">${esc(i.code)} — ${esc(i.name)}</div>
    <div style="font-size:11px;margin-top:2px;display:flex;align-items:center;gap:6px;"><span class="bdg ${i.type==='pinjam'?'b-pinjam':'b-consumable'}" style="font-size:10px;"><i class="${i.type==='pinjam'?'bi bi-arrow-repeat':'bi bi-fire'}"></i> ${i.type==='pinjam'?'Pinjam':'Consumable'}</span><span style="color:var(--muted);">Total: ${i.total_qty}</span></div>
  </div>`).join('');
  dd.style.display='block';
}
function sgDmgInvSelect(id,name,code,type,total){
  _selDmgInv={id,name,code,type,total};
  document.getElementById('d-item').value=id;
  const txt=document.getElementById('d-inv-txt');txt.value='';txt.placeholder=code+' — '+name;
  document.getElementById('d-inv-dd').style.display='none';
  document.getElementById('d-inv-sel-nm').textContent=code+' — '+name;
  document.getElementById('d-inv-sel-info').innerHTML=`<i class="${type==='pinjam'?'bi bi-arrow-repeat text-info':'bi bi-fire text-danger'}"></i> `+(type==='pinjam'?'Pinjam':'Consumable')+' | Total: '+total;
  document.getElementById('d-inv-sel').style.display='flex';
  // Populate info card
  const inv=_dmgInvT.find(i=>i.id==id);
  document.getElementById('d-info-name').textContent=name;
  document.getElementById('d-info-meta').textContent=code+' • '+(inv?.category||'');
  document.getElementById('d-info-stock').textContent=(inv?.available_qty??0)+' unit';
  const lastDmg=_dmgStore.filter(d=>d.inventory_id==id).sort((a,b)=>new Date(b.date)-new Date(a.date))[0];
  const lastEl=document.getElementById('d-info-lastdmg');
  if(lastDmg){
    const diff=Math.round((Date.now()-new Date(lastDmg.date))/(864e5));
    const rel=diff===0?'hari ini':diff===1?'1 hari lalu':diff<7?diff+' hari lalu':diff<14?'1 minggu lalu':diff<30?Math.round(diff/7)+' minggu lalu':Math.round(diff/30)+' bulan lalu';
    lastEl.textContent=rel;lastEl.style.color='var(--danger)';
  } else {
    lastEl.textContent='Belum pernah';lastEl.style.color='var(--success)';
  }
  document.getElementById('d-inv-info').style.display='block';
}
function sgDmgInvClear(){
  _selDmgInv=null;
  document.getElementById('d-item').value='';
  const txt=document.getElementById('d-inv-txt');txt.value='';txt.placeholder='Cari kode / nama barang...';
  document.getElementById('d-inv-sel').style.display='none';
  document.getElementById('d-inv-info').style.display='none';
}
function sgDmgInvBlur(){setTimeout(()=>{const dd=document.getElementById('d-inv-dd');if(dd)dd.style.display='none';},200);}

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
      <div class="dg-full"><div class="dg-lbl">Foto Kerusakan</div>${photoThumb(d.damage_photo_url,'Foto Kerusakan')}</div>
    </div>
    ${fromTrx?`
    <div style="background:rgba(62,207,255,.07);border:1px solid rgba(62,207,255,.25);border-radius:10px;padding:14px;margin-top:4px;">
      <div style="font-weight:700;font-size:13px;margin-bottom:10px;"><i class="bi bi-arrow-return-left"></i> Asal: Dari Pengembalian Transaksi</div>
      <div class="detail-grid" style="margin-bottom:0;">
        <div><div class="dg-lbl">ID Transaksi</div><code style="font-size:12px;word-break:break-all;">${esc(d.transaction_code||'—')}</code></div>
        <div><div class="dg-lbl">Peminjam</div><div class="dg-val">${esc(d.borrower_name||'—')}</div></div>
        <div><div class="dg-lbl">Tanggal Pinjam</div><div class="dg-val">${fdt(d.loan_date)}</div></div>
        <div><div class="dg-lbl">Catatan Kondisi</div><div class="dg-val">${d.condition_notes?esc(d.condition_notes):'<span style="color:var(--muted);font-style:italic;font-size:12px;">—</span>'}</div></div>
      </div>
    </div>`:`
    <div style="background:var(--bg);border-radius:10px;padding:12px 14px;font-size:13px;color:var(--muted);">
      <i class="bi bi-pencil-square me-1"></i> Sumber: <strong>Input manual</strong> (bukan dari pengembalian)
    </div>`}`;
  new bootstrap.Modal(document.getElementById('mdl-dmg-detail')).show();
}

async function saveDmg(){
  const invId=document.getElementById('d-item').value;
  const qty=document.getElementById('d-qty').value;
  const desc=document.getElementById('d-desc').value.trim();
  if(!invId){toast('Pilih barang!','warning');return;}
  if(!desc){toast('Keterangan wajib diisi!','warning');return;}
  if(parseInt(qty)<1){toast('Jumlah minimal 1!','warning');return;}
  if(!pwValidate('dmg_manual','Foto Kerusakan'))return;
  const fd=new FormData();
  fd.append('inventory_id',invId);
  fd.append('qty',qty);
  fd.append('description',desc);
  fd.append('damage_photo',getPhotoFile('dmg_manual'));
  ld(true);
  try{
    const res=await apiForm('{{ route("damaged.store") }}',fd);
    ld(false);toast(res.message);
    document.getElementById('d-qty').value='1';
    document.getElementById('d-desc').value='';
    sgDmgInvClear();
    _pwClear('dmg_manual');
    loadDmg();
  }catch(e){ld(false);toast(e.message,'danger');}
}

function openDmgScanner(){
  if(typeof Html5Qrcode==='undefined'){toast('Library scanner tidak tersedia. Coba refresh halaman.','danger');return;}
  document.getElementById('dmg-scan-status-txt').innerHTML='<i class="bi bi-hourglass-split me-1"></i>Menginisialisasi kamera...';
  _dmgScannerModal=new bootstrap.Modal(document.getElementById('mdl-dmg-scanner'));
  _dmgScannerModal.show();
  document.getElementById('mdl-dmg-scanner').addEventListener('shown.bs.modal',_startDmgScanner,{once:true});
}
function _startDmgScanner(){
  const config={fps:10,qrbox:{width:230,height:230},aspectRatio:1.0,rememberLastUsedCamera:true};
  _dmgQrScanner=new Html5Qrcode('dmg-qr-reader');
  Html5Qrcode.getCameras().then(cameras=>{
    if(!cameras||!cameras.length){document.getElementById('dmg-scan-status-txt').innerHTML='<i class="bi bi-exclamation-triangle text-warning me-1"></i>Tidak ada kamera ditemukan';return;}
    const virtualRe=/obs|virtual|screen|capture|dshow|snap|zoom|teams|skype|manycam|wirecast|xsplit/i;
    const physical=cameras.filter(c=>!virtualRe.test(c.label));
    const pool=physical.length?physical:cameras;
    const selected=pool.find(c=>/back|rear|environment/i.test(c.label))||pool.find(c=>/integrated|built.?in|facetime|webcam|hd cam|usb cam|laptop/i.test(c.label))||pool[0];
    _dmgQrScanner.start(selected.id,config,_onDmgScanSuccess,()=>{}).then(()=>{
      document.getElementById('dmg-scan-status-txt').innerHTML='<i class="bi bi-camera-fill text-success me-1"></i>Arahkan kamera ke QR / barcode barang...';
    }).catch(()=>{
      document.getElementById('dmg-scan-status-txt').innerHTML='<i class="bi bi-exclamation-circle text-danger me-1"></i>Izin kamera diperlukan. Izinkan akses kamera di browser.';
    });
  }).catch(()=>{
    document.getElementById('dmg-scan-status-txt').innerHTML='<i class="bi bi-exclamation-circle text-danger me-1"></i>Izin kamera ditolak. Periksa pengaturan browser.';
  });
}
function _onDmgScanSuccess(decodedText){
  const code=decodedText.trim();
  closeDmgScanner();
  const exact=_dmgInvT.find(i=>i.code===code);
  if(exact){
    sgDmgInvSelect(exact.id,exact.name,exact.code,exact.type,exact.total_qty);
    toast('Barang berhasil dipindai: '+exact.code+' — '+exact.name,'success');
  } else {
    document.getElementById('d-inv-txt').value=code;
    sgDmgInvInput(code);
    const partial=_dmgInvT.filter(i=>(i.code||'').toLowerCase().includes(code.toLowerCase())||(i.name||'').toLowerCase().includes(code.toLowerCase()));
    toast(partial.length?'Kode dipindai: '+code+' — Pilih dari daftar':'Barang dengan kode "'+code+'" tidak ditemukan',partial.length?'info':'warning');
  }
}
function closeDmgScanner(){
  if(_dmgQrScanner){
    _dmgQrScanner.isScanning?_dmgQrScanner.stop().then(()=>{_dmgQrScanner.clear();_dmgQrScanner=null;}).catch(()=>{_dmgQrScanner=null;}):(_dmgQrScanner.clear(),_dmgQrScanner=null);
  }
  if(_dmgScannerModal){_dmgScannerModal.hide();_dmgScannerModal=null;}
}
document.getElementById('mdl-dmg-scanner').addEventListener('hide.bs.modal',()=>{
  if(_dmgQrScanner&&_dmgQrScanner.isScanning){_dmgQrScanner.stop().catch(()=>{});_dmgQrScanner=null;}
});

initDmg();
</script>
@endpush
