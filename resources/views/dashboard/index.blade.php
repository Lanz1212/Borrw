@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div id="dash-content">
  <div class="d-flex align-items-center gap-2" style="color:var(--muted);font-size:14px;padding:20px 0;">
    <div class="spinner-border spinner-border-sm" style="color:var(--accent);"></div> Memuat statistik...
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(async function(){
  ld(true);
  let res;
  try{ res = await api('{{ route("dashboard.stats") }}'); } catch(e){ ld(false); toast(e.message,'danger'); return; }
  ld(false);
  if(!res.success){ document.getElementById('dash-content').innerHTML=`<div class="alert alert-danger">Gagal memuat statistik.</div>`; return; }
  const d = res.data;

  const lowH = d.lowStock && d.lowStock.length
    ? d.lowStock.map(i=>`<div class="d-flex justify-content-between align-items-center py-2 border-bottom"><span style="font-size:13px;" class="text-truncate pe-2">${esc(i.name)}</span><span class="low-pill flex-shrink-0"><i class="bi bi-exclamation-triangle"></i>${i.available}/${i.minStock}</span></div>`).join('')
    : '<p style="color:var(--muted);font-size:13px;text-align:center;padding:14px 0;"><i class="bi bi-check-circle-fill text-success"></i> Semua stok aman</p>';

  document.getElementById('dash-content').innerHTML = `
    <div class="row g-3 mb-3">
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card sc-orange"><div class="stat-ico si-orange"><i class="bi bi-box-seam"></i></div><div class="stat-val">${d.totalItems}</div><div class="stat-lbl">Jenis Barang</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card sc-green"><div class="stat-ico si-green"><i class="bi bi-check-circle-fill"></i></div><div class="stat-val" style="color:var(--success);">${d.availableItems}</div><div class="stat-lbl">Unit Tersedia</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card sc-blue"><div class="stat-ico si-blue"><i class="bi bi-arrow-repeat"></i></div><div class="stat-val" style="color:var(--info);">${d.borrowedItems}</div><div class="stat-lbl">Sedang Dipinjam</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card sc-red"><div class="stat-ico si-red"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="stat-val" style="color:var(--danger);">${d.totalDamaged}</div><div class="stat-lbl">Total Rusak (unit)</div></div></div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-12 col-xl-8"><div class="card p20"><div class="st mb-3"><i class="bi bi-graph-up text-primary"></i> Transaksi 30 Hari Terakhir</div><div style="width:100%;overflow-x:auto;"><canvas id="chart-m" style="min-width:400px;height:200px;"></canvas></div></div></div>
      <div class="col-12 col-xl-4"><div class="card p20 h-100"><div class="st mb-2"><i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis</div><div style="overflow-y:auto;max-height:240px;padding-right:4px;">${lowH}</div></div></div>
    </div>
    <div class="card p20">
      <div class="st mb-3"><i class="bi bi-lightning-charge" style="color:var(--accent);"></i> Aksi Cepat</div>
      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('transactions.index') }}" class="b-acc flex-grow-1 flex-md-grow-0"><i class="bi bi-plus-circle"></i> Transaksi Baru</a>
        <a href="{{ route('returns.index') }}" class="b-out flex-grow-1 flex-md-grow-0"><i class="bi bi-arrow-return-left"></i> Pengembalian</a>
        <a href="{{ route('inventory.index') }}" class="b-out flex-grow-1 flex-md-grow-0"><i class="bi bi-box-seam"></i> Inventori</a>
      </div>
    </div>`;

  if(d.dailyStats && d.dailyStats.length){
    new Chart(document.getElementById('chart-m'),{
      type:'line',
      data:{
        labels:d.dailyStats.map(m=>m.label),
        datasets:[
          {label:'Pinjam',data:d.dailyStats.map(m=>m.pinjam),backgroundColor:'rgba(59,130,246,.1)',borderColor:'rgba(59,130,246,.9)',borderWidth:2,tension:.3,fill:true,pointRadius:2,pointHoverRadius:5},
          {label:'Consumable',data:d.dailyStats.map(m=>m.consumable),backgroundColor:'rgba(249,115,22,.1)',borderColor:'rgba(249,115,22,.9)',borderWidth:2,tension:.3,fill:true,pointRadius:2,pointHoverRadius:5}
        ]
      },
      options:{responsive:true,maintainAspectRatio:false,
        plugins:{legend:{position:'bottom',labels:{font:{family:'Poppins',size:12},usePointStyle:true,pointStyleWidth:8,padding:16}}},
        scales:{
          y:{beginAtZero:true,ticks:{stepSize:1,font:{family:'Poppins',size:11}},grid:{color:'rgba(0,0,0,.04)'}},
          x:{ticks:{font:{family:'Poppins',size:10},maxRotation:45,autoSkip:true,maxTicksLimit:10},grid:{display:false}}
        }
      }
    });
  }
})();
</script>
@endpush
