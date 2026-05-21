@extends('layouts.app')
@section('title','Manajemen User')
@section('page-title','Manajemen User')

@section('content')
<div class="sh">
  <span class="st"><i class="bi bi-people text-primary"></i> Manajemen User</span>
  <button class="b-acc" onclick="openUsrMdl()"><i class="bi bi-person-plus"></i> Tambah User</button>
</div>

<div class="tw">
  <table class="table table-hover">
    <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
    <tbody id="usr-tb"><tr><td colspan="6" class="text-center py-4" style="color:var(--muted);">Memuat...</td></tr></tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="mdl-usr" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usr-mt"><i class="bi bi-person-plus"></i> Tambah User</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="usr-id">
        <div class="fgrp"><label class="flbl">Nama Lengkap *</label><input id="usr-nm" class="fc w-100" placeholder="Nama lengkap"></div>
        <div class="fgrp"><label class="flbl">Username *</label><input id="usr-un" class="fc w-100" placeholder="username"></div>
        <div class="fgrp"><label class="flbl">Email *</label><input type="email" id="usr-em" class="fc w-100" placeholder="email@domain.com"></div>
        <div class="fgrp">
          <label class="flbl">Password <small id="usr-ph" class="text-muted">(wajib untuk user baru)</small></label>
          <input type="password" id="usr-pw" class="fc w-100" placeholder="Password">
        </div>
        <div class="fgrp"><label class="flbl">Role</label>
          <select id="usr-rl" class="fs w-100"><option value="user">User</option><option value="admin">Admin</option></select>
        </div>
        <div class="fgrp"><label class="flbl">Status</label>
          <select id="usr-ac" class="fs w-100"><option value="1">Aktif</option><option value="0">Nonaktif</option></select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="b-out" data-bs-dismiss="modal">Batal</button>
        <button class="b-acc" onclick="saveUsr()"><i class="bi bi-check-lg"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const CURRENT_USER_ID = {{ auth()->id() }};

async function loadUsr(){
  ld(true);
  try{
    const res = await api('{{ route("users.data") }}');
    ld(false);
    renderUsrRows(res.data || []);
  }catch(e){ld(false);toast(e.message,'danger');}
}

function renderUsrRows(users){
  const tb = document.getElementById('usr-tb');
  if(!users.length){
    tb.innerHTML=`<tr><td colspan="6"><div class="empty"><div class="ei"><i class="bi bi-person-fill"></i></div><p>Belum ada user</p></div></td></tr>`;
    return;
  }
  tb.innerHTML = users.map(u=>`<tr>
    <td style="font-weight:500;">${esc(u.name)}</td>
    <td><code style="font-size:12px;">${esc(u.username)}</code></td>
    <td><span class="bdg b-${esc(u.role)}"><i class="${u.role==='admin'?'bi bi-shield-lock':'bi bi-person'}"></i> ${esc(statusLabel(u.role))}</span></td>
    <td><span class="bdg ${u.active?'b-aktif':'b-rusak'}">${u.active?'Aktif':'Nonaktif'}</span></td>
    <td style="font-size:12px;">${fd(u.created_at)}</td>
    <td style="white-space:nowrap;">
      <button class="btn btn-sm btn-outline-primary me-1" onclick='openUsrMdl(${JSON.stringify(u)})'><i class="bi bi-pencil"></i></button>
      ${u.id!==CURRENT_USER_ID?`<button class="btn btn-sm btn-outline-danger" onclick="delUsr(${u.id},'${esc(u.name)}')"><i class="bi bi-trash"></i></button>`:'<span style="font-size:11px;color:var(--muted);">(kamu)</span>'}
    </td>
  </tr>`).join('');
}

function openUsrMdl(u){
  document.getElementById('usr-mt').innerHTML = u ? '<i class="bi bi-pencil-square"></i> Edit User' : '<i class="bi bi-person-plus"></i> Tambah User Baru';
  document.getElementById('usr-id').value = u ? u.id : '';
  document.getElementById('usr-nm').value = u ? u.name : '';
  document.getElementById('usr-un').value = u ? u.username : '';
  document.getElementById('usr-un').disabled = !!u;
  document.getElementById('usr-em').value = u ? (u.email||'') : '';
  document.getElementById('usr-pw').value = '';
  document.getElementById('usr-rl').value = u ? u.role : 'user';
  document.getElementById('usr-ac').value = u ? (u.active?'1':'0') : '1';
  document.getElementById('usr-ph').textContent = u ? '(kosongkan jika tidak diubah)' : '(wajib untuk user baru)';
  new bootstrap.Modal(document.getElementById('mdl-usr')).show();
}

async function saveUsr(){
  const id = document.getElementById('usr-id').value;
  const data = {
    name: document.getElementById('usr-nm').value.trim(),
    username: document.getElementById('usr-un').value.trim(),
    email: document.getElementById('usr-em').value.trim(),
    password: document.getElementById('usr-pw').value,
    role: document.getElementById('usr-rl').value,
    active: document.getElementById('usr-ac').value === '1',
  };
  if(!data.name||!data.username){toast('Nama dan username wajib!','warning');return;}
  if(!data.email){toast('Email wajib diisi!','warning');return;}
  if(!id&&!data.password){toast('Password wajib untuk user baru!','warning');return;}
  ld(true);
  try{
    const url = id ? `/users/${id}` : '{{ route("users.store") }}';
    const res = await api(url, id?'PUT':'POST', data);
    ld(false);toast(res.message);
    bootstrap.Modal.getInstance(document.getElementById('mdl-usr'))?.hide();
    loadUsr();
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function delUsr(id, name){
  if(!confirm(`Hapus user "${name}"?`))return;
  ld(true);
  try{
    const res = await api(`/users/${id}`,'DELETE');
    ld(false);toast(res.message);loadUsr();
  }catch(e){ld(false);toast(e.message,'danger');}
}

loadUsr();
</script>
@endpush
