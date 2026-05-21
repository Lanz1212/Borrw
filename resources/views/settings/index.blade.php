@extends('layouts.app')
@section('title','Pengaturan')
@section('page-title','Pengaturan')

@section('content')
<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-gear text-primary"></i> Pengaturan Umum</div>
      <div class="fgrp"><label class="flbl">Nama Aplikasi</label><input id="s-nm" class="fc w-100" placeholder="Contoh: Sparepart MS"></div>
      <div class="fgrp"><label class="flbl">Nama Perusahaan</label><input id="s-co" class="fc w-100" placeholder="Contoh: PT. Perusahaan Anda"></div>
      <button class="b-acc" onclick="saveSet()"><i class="bi bi-check-lg"></i> Simpan Pengaturan</button>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-folder2-open text-primary"></i> Kategori Barang</div>
      <div class="fgrp">
        <label class="flbl">Kategori (pisahkan koma)</label>
        <textarea id="s-ct" class="fc w-100" rows="5" placeholder="Mekanik,Elektrik,Hidrolik,Pneumatik,Umum"></textarea>
        <small style="color:var(--muted);font-size:11px;">Contoh: Mekanik,Elektrik,Hidrolik,Pneumatik</small>
      </div>
      <button class="b-acc" onclick="saveSet()"><i class="bi bi-check-lg"></i> Simpan Kategori</button>
    </div>
  </div>
  <div class="col-12">
    <div class="card p20">
      <div class="st mb-2"><i class="bi bi-info-circle text-primary"></i> Informasi Sistem</div>
      <div class="row g-3 mt-1">
        <div class="col-6 col-md-3"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Framework</div><div style="font-weight:500;margin-top:3px;">Laravel 12</div></div>
        <div class="col-6 col-md-3"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Database</div><div style="font-weight:500;margin-top:3px;">{{ config('database.default') }}</div></div>
        <div class="col-6 col-md-3"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;">PHP Version</div><div style="font-weight:500;margin-top:3px;">{{ PHP_VERSION }}</div></div>
        <div class="col-6 col-md-3"><div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Environment</div><div style="font-weight:500;margin-top:3px;">{{ app()->environment() }}</div></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function loadSet(){
  ld(true);
  try{
    const res = await api('{{ route("settings.data") }}');
    ld(false);
    const s = res.data || {};
    document.getElementById('s-nm').value = s.app_name || '';
    document.getElementById('s-co').value = s.company_name || '';
    document.getElementById('s-ct').value = s.categories || '';
  }catch(e){ld(false);toast(e.message,'danger');}
}

async function saveSet(){
  const data = {
    app_name: document.getElementById('s-nm').value.trim(),
    company_name: document.getElementById('s-co').value.trim(),
    categories: document.getElementById('s-ct').value.trim(),
  };
  ld(true);
  try{
    const res = await api('{{ route("settings.update") }}','POST',data);
    ld(false);toast(res.message);
    if(data.app_name){
      document.getElementById('app-nm').textContent = data.app_name;
      document.title = data.app_name + ' — Sparepart Management';
    }
  }catch(e){ld(false);toast(e.message,'danger');}
}

loadSet();
</script>
@endpush
