@extends('layouts.app')
@section('title','Inventori Barang')
@section('page-title','Inventori Barang')

@section('content')
<div class="sh">
  <div class="d-flex gap-2 flex-wrap flex-grow-1" style="max-width:580px;">
    <div class="sw"><i class="bi bi-search"></i><input type="text" class="fc w-100" id="inv-q" placeholder="Cari barang..." oninput="loadInv()"></div>
    <select class="fs" id="inv-ft" onchange="loadInv()" style="min-width:150px;flex-shrink:0;">
      <option value="">Semua Jenis</option>
      <option value="pinjam">🔄 Pinjam</option>
      <option value="consumable">🔥 Consumable</option>
    </select>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <button class="b-xl" onclick="exportInventory()"><i class="bi bi-file-earmark-excel"></i> Export</button>
    @if(auth()->user()->isAdmin())
    <button class="b-out" onclick="openImportMdl()"><i class="bi bi-file-earmark-arrow-up"></i> Import</button>
    <button class="b-acc" onclick="openInvMdl()"><i class="bi bi-plus-lg"></i> Tambah</button>
    @endif
  </div>
</div>

<div class="tw">
  <table class="table table-hover">
    <thead><tr>
      <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Jenis</th>
      <th style="text-align:center;">Total</th><th style="text-align:center;">Tersedia</th><th style="text-align:center;">Min</th>
      <th>Kondisi</th><th>Aksi</th>
    </tr></thead>
    <tbody id="inv-tb"><tr><td colspan="9" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="mdl-inv" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="inv-mt"><i class="bi bi-box-seam"></i> Tambah Barang</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="inv-id">
        <div class="row g-3">
          <div class="col-12 col-md-6"><label class="flbl">Kode Barang *</label><input id="inv-code" class="fc" placeholder="Contoh: BLT-001"></div>
          <div class="col-12 col-md-6"><label class="flbl">Nama Barang *</label><input id="inv-name" class="fc" placeholder="Nama barang"></div>
          <div class="col-12 col-md-6"><label class="flbl">Kategori *</label><input id="inv-cat" class="fc" list="cat-list" placeholder="Pilih atau ketik kategori"></div>
          <datalist id="cat-list" id="cat-list"></datalist>
          <div class="col-12 col-md-6"><label class="flbl">Jenis Barang *</label>
            <select id="inv-type" class="fs">
              <option value="pinjam">🔄 Barang Pinjam (returnable)</option>
              <option value="consumable">🔥 Consumable (habis pakai)</option>
            </select>
          </div>
          <div class="col-12 col-md-4"><label class="flbl">Jumlah Total *</label><input type="number" id="inv-tot" class="fc" min="0" value="0"></div>
          <div class="col-12 col-md-4"><label class="flbl">Tersedia *</label><input type="number" id="inv-av" class="fc" min="0" value="0"></div>
          <div class="col-12 col-md-4"><label class="flbl">Min Stok</label><input type="number" id="inv-mn" class="fc" min="0" value="0"></div>
          <div class="col-12 col-md-6"><label class="flbl">Kondisi</label>
            <select id="inv-cond" class="fs">
              <option value="baik">✅ Baik</option>
              <option value="perlu_perbaikan">⚠️ Perlu Perbaikan</option>
              <option value="rusak">❌ Rusak</option>
            </select>
          </div>
          <div class="col-12 col-md-6"><label class="flbl">Catatan</label><input id="inv-nt" class="fc" placeholder="Opsional"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="b-out" data-bs-dismiss="modal">Batal</button>
        <button class="b-acc" onclick="saveInv()"><i class="bi bi-check-lg"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
@if(auth()->user()->isAdmin())
<!-- Import Modal -->
<div class="modal fade" id="mdl-import" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-file-earmark-arrow-up"></i> Import Barang dari Excel</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div style="background:rgba(59,130,246,.06);border:1px solid rgba(59,130,246,.2);border-radius:9px;padding:12px 16px;margin-bottom:14px;font-size:12.5px;">
          <strong><i class="bi bi-info-circle me-1"></i>Panduan:</strong>
          <ul style="margin:6px 0 0;padding-left:18px;">
            <li>Format file: <strong>.xlsx</strong> atau <strong>.csv</strong></li>
            <li>Kolom wajib: <strong>Kode</strong> dan <strong>Nama Barang</strong></li>
            <li>Jika kode sudah ada → <strong>diperbarui</strong>. Belum ada → <strong>ditambahkan baru</strong>.</li>
            <li>Tipe valid: <code>pinjam</code> atau <code>consumable</code> &nbsp;|&nbsp; Kondisi: <code>baik</code>, <code>rusak</code>, <code>perlu_perbaikan</code></li>
          </ul>
        </div>
        <button class="b-out mb-3" style="font-size:12.5px;padding:7px 14px;" onclick="downloadTemplate()"><i class="bi bi-download me-1"></i> Download Template Excel</button>
        <div class="fgrp">
          <label class="flbl">Pilih File (.xlsx / .csv)</label>
          <input type="file" id="imp-file" class="fc" accept=".xlsx,.xls,.csv" onchange="handleImportFile(this)">
        </div>
        <div id="imp-preview" style="display:none;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Preview: <strong id="imp-count">0</strong> baris valid</div>
          <div class="tw" style="max-height:260px;overflow-y:auto;">
            <table class="table" style="font-size:12px;min-width:580px;">
              <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Tipe</th><th style="text-align:center;">Total</th><th style="text-align:center;">Tersedia</th><th style="text-align:center;">Min</th><th>Kondisi</th></tr></thead>
              <tbody id="imp-tb"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="b-out" data-bs-dismiss="modal">Batal</button>
        <button class="b-acc" id="btn-imp-sub" onclick="submitImport()" disabled><i class="bi bi-cloud-upload"></i> Upload & Simpan</button>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const IS_ADMIN_INV = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
const CATS_DEFAULT = {!! json_encode(\App\Models\Setting::get('categories','Mekanik,Elektrik,Hidrolik,Pneumatik,Umum')) !!}.split(',').map(c=>c.trim());
let _inv = [];

async function loadInv(){
  const q = document.getElementById('inv-q').value;
  const type = document.getElementById('inv-ft').value;
  let url = '{{ route("inventory.data") }}?';
  if(q) url += 'q='+encodeURIComponent(q)+'&';
  if(type) url += 'type='+encodeURIComponent(type);
  ld(true);
  try{
    const res = await api(url);
    ld(false);
    _inv = res.data || [];
    renderInvRows(_inv);
  } catch(e){ld(false);toast(e.message,'danger');}
}

function renderInvRows(items){
  const tb = document.getElementById('inv-tb');
  const cols = 9;
  if(!items.length){
    tb.innerHTML=`<tr><td colspan="${cols}"><div class="empty"><div class="ei"><i class="bi bi-box-seam"></i></div><p>Tidak ada data</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = items.map(i=>{
    const lo = i.min_stock>0 && i.available_qty<=i.min_stock;
    return `<tr>
      <td><code style="font-size:11px;">${esc(i.code)}</code></td>
      <td><div style="font-weight:500;">${esc(i.name)}</div>${lo?'<span class="low-pill mt-1"><i class="bi bi-exclamation-triangle"></i> Menipis</span>':''}</td>
      <td><span class="bdg b-cat">${esc(i.category)}</span></td>
      <td><span class="bdg ${i.type==='pinjam'?'b-pinjam':'b-consumable'}"><i class="${i.type==='pinjam'?'bi bi-arrow-repeat':'bi bi-fire'}"></i> ${i.type==='pinjam'?'Pinjam':'Consumable'}</span></td>
      <td style="text-align:center;">${i.total_qty}</td>
      <td style="text-align:center;"><strong style="color:${lo?'var(--warning)':'var(--success)'};">${i.available_qty}</strong></td>
      <td style="text-align:center;">${i.min_stock}</td>
      <td><span class="bdg b-${esc(i.condition)}">${esc(statusLabel(i.condition))}</span></td>
      <td style="white-space:nowrap;">
        <a href="/inventory/${i.id}/qr" class="btn btn-sm btn-outline-secondary me-1" title="Print QR"><i class="bi bi-qr-code"></i></a>
        ${IS_ADMIN_INV?`
        <button class="btn btn-sm btn-outline-primary me-1" onclick='openInvMdl(${JSON.stringify(i)})'><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="delInv(${i.id},'${esc(i.name)}')"><i class="bi bi-trash"></i></button>
        `:''}
      </td>
    </tr>`;
  }).join('');
}

function openInvMdl(item){
  document.getElementById('inv-mt').innerHTML = item ? '<i class="bi bi-pencil-square"></i> Edit Barang' : '<i class="bi bi-box-seam"></i> Tambah Barang Baru';
  document.getElementById('inv-id').value = item ? item.id : '';
  document.getElementById('inv-code').value = item ? item.code : '';
  document.getElementById('inv-name').value = item ? item.name : '';
  document.getElementById('inv-cat').value = item ? (item.category||'') : '';
  document.getElementById('inv-type').value = item ? item.type : 'pinjam';
  document.getElementById('inv-tot').value = item ? item.total_qty : '0';
  document.getElementById('inv-av').value = item ? item.available_qty : '0';
  document.getElementById('inv-mn').value = item ? item.min_stock : '0';
  document.getElementById('inv-cond').value = item ? item.condition : 'baik';
  document.getElementById('inv-nt').value = item ? (item.notes||'') : '';
  const dl = document.getElementById('cat-list');
  dl.innerHTML = CATS_DEFAULT.map(c=>`<option value="${esc(c)}">`).join('');
  new bootstrap.Modal(document.getElementById('mdl-inv')).show();
}

async function saveInv(){
  const id = document.getElementById('inv-id').value;
  const data = {
    code: document.getElementById('inv-code').value.trim(),
    name: document.getElementById('inv-name').value.trim(),
    category: document.getElementById('inv-cat').value.trim(),
    type: document.getElementById('inv-type').value,
    total_qty: parseInt(document.getElementById('inv-tot').value)||0,
    available_qty: parseInt(document.getElementById('inv-av').value)||0,
    min_stock: parseInt(document.getElementById('inv-mn').value)||0,
    condition: document.getElementById('inv-cond').value,
    notes: document.getElementById('inv-nt').value.trim(),
  };
  if(!data.code||!data.name||!data.category){toast('Kode, nama, dan kategori wajib diisi!','warning');return;}
  ld(true);
  try{
    const url = id ? `/inventory/${id}` : '{{ route("inventory.store") }}';
    const method = id ? 'PUT' : 'POST';
    const res = await api(url, method, data);
    ld(false);
    toast(res.message);
    bootstrap.Modal.getInstance(document.getElementById('mdl-inv'))?.hide();
    loadInv();
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function delInv(id, name){
  if(!confirm(`Hapus barang "${name}"?`))return;
  ld(true);
  try{
    const res = await api(`/inventory/${id}`,'DELETE');
    ld(false);toast(res.message);loadInv();
  }catch(e){ld(false);toast(e.message,'danger');}
}

let _importData=[];

function exportInventory(){
  if(!_inv.length){toast('Tidak ada data untuk diekspor.','warning');return;}
  const rows=[
    ['Kode','Nama Barang','Kategori','Tipe','Total Qty','Tersedia','Min Stok','Kondisi','Catatan'],
    ..._inv.map(i=>[i.code,i.name,i.category,i.type,i.total_qty,i.available_qty,i.min_stock,i.condition,i.notes||''])
  ];
  exportXlsx(rows,'Inventaris_'+dateStr()+'.xlsx');
  toast('Export berhasil diunduh.');
}

function downloadTemplate(){
  const rows=[
    ['Kode','Nama Barang','Kategori','Tipe','Total Qty','Tersedia','Min Stok','Kondisi','Catatan'],
    ['BRG-XXX','Contoh Barang','Mekanik','pinjam',10,10,3,'baik','Opsional'],
    ['KBL-XXX','Contoh Consumable','Elektrik','consumable',50,50,10,'baik',''],
  ];
  exportXlsx(rows,'Template_Import_Inventaris.xlsx');
}

function openImportMdl(){
  _importData=[];
  document.getElementById('imp-file').value='';
  document.getElementById('imp-preview').style.display='none';
  document.getElementById('btn-imp-sub').disabled=true;
  new bootstrap.Modal(document.getElementById('mdl-import')).show();
}

function handleImportFile(input){
  if(!input.files||!input.files[0])return;
  if(typeof XLSX==='undefined'){toast('Library belum dimuat, coba refresh.','warning');return;}
  const reader=new FileReader();
  reader.onload=function(e){
    try{
      const wb=XLSX.read(e.target.result,{type:'binary'});
      const ws=wb.Sheets[wb.SheetNames[0]];
      const data=XLSX.utils.sheet_to_json(ws,{header:1,defval:''});
      if(data.length<2){toast('File kosong atau tidak ada data.','warning');return;}
      const hdr=data[0].map(h=>String(h||'').toLowerCase().trim());
      const get=(row,...keys)=>{for(const k of keys){const i=hdr.findIndex(h=>h.includes(k));if(i>-1)return row[i]||'';}return '';};
      _importData=data.slice(1)
        .filter(row=>row.some(c=>c!==null&&c!==''))
        .map(row=>({
          code:String(get(row,'kode','code')).trim(),
          name:String(get(row,'nama','name')).trim(),
          category:String(get(row,'kategori','category')||'Umum').trim(),
          type:String(get(row,'tipe','type')||'pinjam').trim().toLowerCase(),
          total_qty:parseInt(get(row,'total'))||0,
          available_qty:get(row,'tersedia','available','avail')!==''?parseInt(get(row,'tersedia','available','avail')):null,
          min_stock:parseInt(get(row,'min'))||0,
          condition:String(get(row,'kondisi','condition')||'baik').trim().toLowerCase(),
          notes:String(get(row,'catatan','notes')||'').trim(),
        })).filter(r=>r.code&&r.name);
      if(!_importData.length){toast('Tidak ada baris valid. Pastikan kolom Kode & Nama Barang terisi.','warning');return;}
      document.getElementById('imp-count').textContent=_importData.length;
      document.getElementById('imp-tb').innerHTML=_importData.map(r=>`
        <tr>
          <td><code style="font-size:11px;">${esc(r.code)}</code></td>
          <td>${esc(r.name)}</td><td>${esc(r.category)}</td>
          <td><span class="bdg ${r.type==='pinjam'?'b-pinjam':'b-consumable'}">${r.type}</span></td>
          <td style="text-align:center;">${r.total_qty}</td>
          <td style="text-align:center;">${r.available_qty ?? r.total_qty}</td>
          <td style="text-align:center;">${r.min_stock}</td>
          <td><span class="bdg b-${r.condition}">${statusLabel(r.condition)}</span></td>
        </tr>`).join('');
      document.getElementById('imp-preview').style.display='block';
      document.getElementById('btn-imp-sub').disabled=false;
    }catch(err){toast('Gagal membaca file: '+err.message,'danger');}
  };
  reader.readAsBinaryString(input.files[0]);
}

async function submitImport(){
  if(!_importData.length)return;
  ld(true);
  try{
    const res=await api('{{ route("inventory.import") }}','POST',{items:_importData});
    ld(false);
    toast(res.message);
    bootstrap.Modal.getInstance(document.getElementById('mdl-import'))?.hide();
    loadInv();
  }catch(e){ld(false);toast(e.message,'danger');}
}

loadInv();
</script>
@endpush
