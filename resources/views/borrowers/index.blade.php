@extends('layouts.app')
@section('title','Data Peminjam')
@section('page-title','Data Peminjam')

@section('content')
<div class="sh">
  <div class="d-flex gap-2 flex-wrap flex-grow-1" style="max-width:400px;">
    <div class="sw"><i class="bi bi-search"></i><input type="text" class="fc w-100" id="brw-q" placeholder="Cari peminjam..." oninput="loadBrw()"></div>
  </div>
  <button class="b-acc" onclick="openBrwMdl()"><i class="bi bi-plus-lg"></i> Tambah Peminjam</button>
</div>

<div class="tw">
  <table class="table table-hover">
    <thead><tr><th>Nama</th><th>Kontak</th><th>Departemen</th><th>Catatan</th><th>Aksi</th></tr></thead>
    <tbody id="brw-tb"><tr><td colspan="5" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="mdl-brw" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="brw-mt"><i class="bi bi-person-plus"></i> Tambah Peminjam</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="brw-id">
        <div class="row g-3">
          <div class="col-12"><label class="flbl">Nama *</label><input id="brw-nm" class="fc" placeholder="Nama lengkap"></div>
          <div class="col-12 col-md-6"><label class="flbl">Kontak</label><input id="brw-ct" class="fc" placeholder="No. HP / email"></div>
          <div class="col-12 col-md-6"><label class="flbl">Departemen / Unit</label><input id="brw-dp" class="fc" placeholder="Contoh: Forklift, Maintenance"></div>
          <div class="col-12"><label class="flbl">Catatan</label><input id="brw-nt" class="fc" placeholder="Opsional"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="b-out" data-bs-dismiss="modal">Batal</button>
        <button class="b-acc" onclick="saveBrw()"><i class="bi bi-check-lg"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const IS_ADMIN_BRW = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
let _brw = [];

async function loadBrw(){
  const q = document.getElementById('brw-q').value;
  let url = '{{ route("borrowers.data") }}' + (q ? '?q='+encodeURIComponent(q) : '');
  ld(true);
  try{
    const res = await api(url);
    ld(false);
    _brw = res.data || [];
    renderBrwRows(_brw);
  }catch(e){ld(false);toast(e.message,'danger');}
}

function renderBrwRows(items){
  const tb = document.getElementById('brw-tb');
  if(!items.length){
    tb.innerHTML=`<tr><td colspan="5"><div class="empty"><div class="ei"><i class="bi bi-people"></i></div><p>Belum ada peminjam</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = items.map(b=>`<tr>
    <td style="font-weight:500;">${esc(b.name)}</td>
    <td>${esc(b.contact||'—')}</td>
    <td>${esc(b.department||'—')}</td>
    <td style="max-width:200px;white-space:normal;">${esc(b.notes||'—')}</td>
    <td style="white-space:nowrap;">
      <button class="btn btn-sm btn-outline-primary me-1" onclick='openBrwMdl(${JSON.stringify(b)})'><i class="bi bi-pencil"></i></button>
      ${IS_ADMIN_BRW?`<button class="btn btn-sm btn-outline-danger" onclick="delBrw(${b.id},'${esc(b.name)}')"><i class="bi bi-trash"></i></button>`:''}
    </td>
  </tr>`).join('');
}

function openBrwMdl(b){
  document.getElementById('brw-mt').innerHTML = b ? '<i class="bi bi-pencil-square"></i> Edit Peminjam' : '<i class="bi bi-person-plus"></i> Tambah Peminjam';
  document.getElementById('brw-id').value = b ? b.id : '';
  document.getElementById('brw-nm').value = b ? b.name : '';
  document.getElementById('brw-ct').value = b ? (b.contact||'') : '';
  document.getElementById('brw-dp').value = b ? (b.department||'') : '';
  document.getElementById('brw-nt').value = b ? (b.notes||'') : '';
  new bootstrap.Modal(document.getElementById('mdl-brw')).show();
}

async function saveBrw(){
  const id = document.getElementById('brw-id').value;
  const data = {
    name: document.getElementById('brw-nm').value.trim(),
    contact: document.getElementById('brw-ct').value.trim(),
    department: document.getElementById('brw-dp').value.trim(),
    notes: document.getElementById('brw-nt').value.trim(),
  };
  if(!data.name){toast('Nama wajib diisi!','warning');return;}
  ld(true);
  try{
    const url = id ? `/borrowers/${id}` : '{{ route("borrowers.store") }}';
    const res = await api(url, id?'PUT':'POST', data);
    ld(false);toast(res.message);
    bootstrap.Modal.getInstance(document.getElementById('mdl-brw'))?.hide();
    loadBrw();
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function delBrw(id, name){
  if(!confirm(`Hapus peminjam "${name}"?`))return;
  ld(true);
  try{
    const res = await api(`/borrowers/${id}`,'DELETE');
    ld(false);toast(res.message);loadBrw();
  }catch(e){ld(false);toast(e.message,'danger');}
}

loadBrw();
</script>
@endpush
