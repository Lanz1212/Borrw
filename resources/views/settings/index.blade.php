@extends('layouts.app')
@section('title','Pengaturan')
@section('page-title','Pengaturan')

@push('styles')
<style>
.tog-sw{position:relative;display:inline-flex;align-items:center;cursor:pointer;}
.tog-inp{opacity:0;width:0;height:0;position:absolute;}
.tog-track{width:44px;height:24px;background:#CBD5E1;border-radius:24px;position:relative;transition:background .2s;flex-shrink:0;display:block;}
.tog-track::after{content:'';position:absolute;width:18px;height:18px;background:#fff;border-radius:50%;top:3px;left:3px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.tog-inp:checked+.tog-track{background:var(--accent);}
.tog-inp:checked+.tog-track::after{transform:translateX(20px);}
.set-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid var(--border);}
.set-row:last-of-type{border-bottom:none;padding-bottom:0;}
.set-row:first-of-type{padding-top:0;}
.set-lbl{font-weight:500;font-size:13px;color:var(--primary);}
.set-desc{font-size:11.5px;color:var(--muted);margin-top:2px;line-height:1.4;}
.th-opt{border:2px solid transparent;border-radius:10px;padding:8px 10px;background:var(--bg);cursor:pointer;transition:all .15s;display:flex;flex-direction:column;align-items:center;gap:5px;min-width:68px;font-family:inherit;}
.th-opt:hover{border-color:var(--border);background:var(--card);}
.th-opt.selected{border-color:var(--accent);}
.th-swatch{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;}
.th-lbl{font-size:11px;font-weight:500;color:var(--muted);}
.th-opt.selected .th-lbl{color:var(--accent);font-weight:600;}
.info-box{background:var(--bg);border:1px solid var(--border);border-radius:9px;padding:14px 16px;margin-bottom:12px;}
.info-box:last-child{margin-bottom:0;}
</style>
@endpush

@section('content')
<!-- Restore Confirm Modal -->
<div class="modal fade" id="mdl-restore" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Konfirmasi Restore</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:14px;">
          <p style="font-size:13px;font-weight:600;color:#B91C1C;margin:0 0 5px;"><i class="bi bi-exclamation-octagon-fill me-1"></i> Data lama akan ditimpa seluruhnya!</p>
          <p style="font-size:12.5px;color:#7F1D1D;margin:0;line-height:1.5;">Proses ini <strong>tidak dapat dibatalkan</strong>. Pastikan sudah backup terlebih dahulu sebelum restore.</p>
        </div>
        <p style="font-size:13px;margin:0;">File: <strong id="restore-fn" style="color:var(--accent);word-break:break-all;"></strong></p>
      </div>
      <div class="modal-footer">
        <button class="b-out" data-bs-dismiss="modal">Batal</button>
        <button onclick="doRestore()" style="background:var(--danger);border:none;color:#fff;padding:8px 15px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;">
          <i class="bi bi-arrow-counterclockwise"></i> Ya, Restore Sekarang
        </button>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">

  {{-- Row 1: Pengaturan Umum + Kategori --}}
  <div class="col-12 col-lg-6">
    <div class="card p20" style="height:100%;display:flex;flex-direction:column;">
      <div class="st mb-4"><i class="bi bi-gear text-primary"></i> Pengaturan Umum</div>
      <div class="fgrp">
        <label class="flbl">Nama Aplikasi</label>
        <input id="s-nm" class="fc w-100" placeholder="Contoh: Sparepart MS">
      </div>
      <div style="margin-top:auto;padding-top:4px;">
        <button class="b-acc w-100" onclick="saveGeneral()"><i class="bi bi-check-lg"></i> Simpan Pengaturan</button>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card p20" style="height:100%;display:flex;flex-direction:column;">
      <div class="st mb-4"><i class="bi bi-folder2-open text-primary"></i> Kategori Barang</div>
      <div class="fgrp" style="flex:1;">
        <label class="flbl">Kategori <small style="font-weight:400;color:var(--muted);">(pisahkan koma)</small></label>
        <textarea id="s-ct" class="fc w-100" rows="4" placeholder="Mekanik,Elektrik,Hidrolik,Pneumatik,Umum" style="height:100%;min-height:80px;"></textarea>
        <small style="color:var(--muted);font-size:11px;margin-top:4px;display:block;">Contoh: Mekanik,Elektrik,Hidrolik,Pneumatik</small>
      </div>
      <button class="b-acc w-100" onclick="saveCategories()"><i class="bi bi-check-lg"></i> Simpan Kategori</button>
    </div>
  </div>

  {{-- Row 2: Session & Keamanan + Backup --}}
  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-4"><i class="bi bi-shield-lock text-primary"></i> Session & Keamanan</div>

      <div class="set-row">
        <div style="flex:1;min-width:0;">
          <div class="set-lbl"><i class="bi bi-clock me-1" style="color:var(--muted);"></i>Auto Logout</div>
          <div class="set-desc">Logout otomatis jika tidak ada aktivitas</div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
          <input type="number" id="s-to" class="fc fc-sm" min="1" max="1440" value="30" style="width:68px;text-align:center;">
          <span style="font-size:12px;color:var(--muted);white-space:nowrap;">menit</span>
        </div>
      </div>

      <div class="set-row">
        <div style="flex:1;min-width:0;">
          <div class="set-lbl"><i class="bi bi-devices me-1" style="color:var(--muted);"></i>Izinkan Multi Login</div>
          <div class="set-desc">Akun dapat login dari beberapa perangkat sekaligus</div>
        </div>
        <label class="tog-sw" style="flex-shrink:0;">
          <input type="checkbox" class="tog-inp" id="s-ml">
          <span class="tog-track"></span>
        </label>
      </div>

      <div style="margin-top:18px;">
        <button class="b-acc w-100" onclick="saveSession()"><i class="bi bi-shield-check"></i> Simpan Keamanan</button>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-4"><i class="bi bi-database text-primary"></i> Backup Database</div>

      <div class="info-box">
        <div style="font-size:13px;font-weight:600;margin-bottom:5px;"><i class="bi bi-cloud-download me-1" style="color:var(--info);"></i>Backup Sekarang</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:12px;line-height:1.5;">Unduh seluruh data & struktur tabel sebagai file <code>.sql</code>.</div>
        <button class="b-out w-100" onclick="doBackup()"><i class="bi bi-download"></i> Download Backup (.sql)</button>
      </div>

      <div class="info-box" style="border-color:#FED7AA;">
        <div style="font-size:13px;font-weight:600;margin-bottom:5px;"><i class="bi bi-cloud-upload me-1 text-warning"></i>Restore Database</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:12px;line-height:1.5;">Upload file <code>.sql</code> untuk menggantikan seluruh data. <span style="color:var(--danger);font-weight:500;">Tindakan ini tidak dapat dibatalkan!</span></div>
        <label class="b-out w-100" style="cursor:pointer;justify-content:center;">
          <i class="bi bi-upload"></i> Pilih File SQL...
          <input type="file" id="restore-file" accept=".sql,.txt" style="display:none;" onchange="confirmRestore(this)">
        </label>
      </div>
    </div>
  </div>

  {{-- Row 3: Tampilan Aplikasi --}}
  <div class="col-12">
    <div class="card p20">
      <div class="st mb-4"><i class="bi bi-palette text-primary"></i> Tampilan Aplikasi</div>
      <div class="row g-4">
        <div class="col-12 col-sm-7">
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">Warna Tema</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="th-opt" data-th="orange" onclick="applyTheme('orange')">
              <div class="th-swatch" style="background:#F97316;"><i class="bi bi-check-lg" id="tc-orange" style="display:none;"></i></div>
              <span class="th-lbl">Orange</span>
            </button>
            <button class="th-opt" data-th="blue" onclick="applyTheme('blue')">
              <div class="th-swatch" style="background:#2563EB;"><i class="bi bi-check-lg" id="tc-blue" style="display:none;"></i></div>
              <span class="th-lbl">Blue</span>
            </button>
            <button class="th-opt" data-th="green" onclick="applyTheme('green')">
              <div class="th-swatch" style="background:#059669;"><i class="bi bi-check-lg" id="tc-green" style="display:none;"></i></div>
              <span class="th-lbl">Green</span>
            </button>
            <button class="th-opt" data-th="dark" onclick="applyTheme('dark')">
              <div class="th-swatch" style="background:#7C3AED;"><i class="bi bi-check-lg" id="tc-dark" style="display:none;"></i></div>
              <span class="th-lbl">Purple</span>
            </button>
          </div>
          <div style="font-size:11.5px;color:var(--muted);margin-top:10px;"><i class="bi bi-info-circle me-1"></i>Perubahan tema disimpan otomatis.</div>
        </div>
        <div class="col-12 col-sm-5">
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">Mode Tampilan</div>
          <div class="set-row" style="padding:0;border:none;">
            <div style="flex:1;min-width:0;">
              <div class="set-lbl"><i class="bi bi-moon me-1" style="color:var(--muted);"></i>Dark Mode</div>
              <div class="set-desc">Ubah tampilan ke tema gelap</div>
            </div>
            <label class="tog-sw" style="flex-shrink:0;">
              <input type="checkbox" class="tog-inp" id="s-dk" onchange="toggleDark(this.checked)">
              <span class="tog-track"></span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
const THEMES={
  orange:{a:'#F97316',ad:'#EA6A0A',nb:'rgba(249,115,22,.08)',nt:'#FB923C',fr:'rgba(249,115,22,.08)'},
  blue:  {a:'#2563EB',ad:'#1D4ED8',nb:'rgba(37,99,235,.1)', nt:'#60A5FA',fr:'rgba(37,99,235,.1)'},
  green: {a:'#059669',ad:'#047857',nb:'rgba(5,150,105,.1)',  nt:'#34D399',fr:'rgba(5,150,105,.1)'},
  dark:  {a:'#7C3AED',ad:'#6D28D9',nb:'rgba(124,58,237,.1)','nt':'#A78BFA',fr:'rgba(124,58,237,.1)'},
};

async function loadSet(){
  ld(true);
  try{
    const res=await api('{{ route("settings.data") }}');
    ld(false);
    const s=res.data||{};
    document.getElementById('s-nm').value=s.app_name||'';
    document.getElementById('s-ct').value=s.categories||'';
    document.getElementById('s-to').value=s.session_timeout||'30';
    document.getElementById('s-ml').checked=s.multi_login==='1';
    const theme=s.theme||localStorage.getItem('borrw_theme')||'orange';
    const dark=s.dark_mode==='1'||(s.dark_mode===undefined&&localStorage.getItem('borrw_dark')==='1');
    document.getElementById('s-dk').checked=dark;
    markTheme(theme);
    localStorage.setItem('borrw_theme',theme);
    localStorage.setItem('borrw_dark',dark?'1':'0');
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function saveGeneral(){
  const nm=document.getElementById('s-nm').value.trim();
  ld(true);
  try{
    const res=await api('{{ route("settings.update") }}','POST',{app_name:nm});
    ld(false);toast(res.message);
    if(nm){document.getElementById('app-nm').textContent=nm;document.title=nm+' — Sparepart Management';}
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function saveCategories(){
  ld(true);
  try{
    const res=await api('{{ route("settings.update") }}','POST',{categories:document.getElementById('s-ct').value.trim()});
    ld(false);toast(res.message);
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function saveSession(){
  const to=parseInt(document.getElementById('s-to').value)||30;
  if(to<1||to>1440){toast('Timeout harus antara 1–1440 menit.','warning');return;}
  const ml=document.getElementById('s-ml').checked?'1':'0';
  ld(true);
  try{
    const res=await api('{{ route("settings.update") }}','POST',{session_timeout:to,multi_login:ml});
    ld(false);toast(res.message);
  }catch(e){ld(false);toast(e.message,'danger');}
}

function doBackup(){
  toast('Menyiapkan file backup...','info');
  window.location.href='{{ route("settings.backup") }}';
}

let _restoreFile=null;
function confirmRestore(input){
  if(!input.files.length)return;
  _restoreFile=input.files[0];
  document.getElementById('restore-fn').textContent=_restoreFile.name;
  new bootstrap.Modal(document.getElementById('mdl-restore')).show();
  input.value='';
}

async function doRestore(){
  if(!_restoreFile){toast('Tidak ada file dipilih.','warning');return;}
  bootstrap.Modal.getInstance(document.getElementById('mdl-restore'))?.hide();
  ld(true);
  try{
    const fd=new FormData();
    fd.append('sql_file',_restoreFile);
    const r=await fetch('{{ route("settings.restore") }}',{method:'POST',headers:{'X-CSRF-TOKEN':CSRF},body:fd});
    const json=await r.json();
    ld(false);
    toast(json.message,json.success?'success':'danger');
  }catch(e){ld(false);toast(e.message,'danger');}
  _restoreFile=null;
}

function applyTheme(name){
  const th=THEMES[name]||THEMES.orange;
  const r=document.documentElement;
  r.style.setProperty('--accent',th.a);
  r.style.setProperty('--accent-d',th.ad);
  r.style.setProperty('--nav-active-bg',th.nb);
  r.style.setProperty('--nav-active-txt',th.nt);
  r.style.setProperty('--focus-ring',th.fr);
  localStorage.setItem('borrw_theme',name);
  markTheme(name);
  api('{{ route("settings.update") }}','POST',{theme:name}).catch(()=>{});
  toast('Tema berhasil diubah.','success');
}

function markTheme(name){
  document.querySelectorAll('.th-opt').forEach(b=>{
    const sel=b.dataset.th===name;
    b.classList.toggle('selected',sel);
    const ic=document.getElementById('tc-'+b.dataset.th);
    if(ic)ic.style.display=sel?'':'none';
  });
}

function toggleDark(on){
  const r=document.documentElement;
  on?r.setAttribute('data-dark','1'):r.removeAttribute('data-dark');
  localStorage.setItem('borrw_dark',on?'1':'0');
  api('{{ route("settings.update") }}','POST',{dark_mode:on?'1':'0'}).catch(()=>{});
}

loadSet();
</script>
@endpush
