@extends('backend.layout')
@section('title','Thống kê Sản phẩm')
@section('content')
<div class="wrapper wrapper-content">
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>Thống kê Sản phẩm</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="active"><strong>Thống kê Sản phẩm</strong></li>
      </ol>
    </div>
  </div>

  <div class="row mt10">
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Tổng sản phẩm</h5><h2 class="no-margins text-primary">{{ number_format($stats['total']) }}</h2><small>Tất cả sản phẩm</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Đang kinh doanh</h5><h2 class="no-margins text-success">{{ number_format($stats['active']) }}</h2><small>Tỉ lệ: {{ $stats['total'] ? number_format($stats['active']*100/$stats['total'],1) : 0 }}%</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Ngừng kinh doanh</h5><h2 class="no-margins text-warning">{{ number_format($stats['inactive']) }}</h2><small>Tỉ lệ: {{ $stats['total'] ? number_format($stats['inactive']*100/$stats['total'],1) : 0 }}%</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Tổng tồn kho</h5><h2 class="no-margins text-info">{{ number_format($stats['total_stock']) }}</h2><small>Tất cả biến thể</small></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-9">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Sản phẩm mới theo ngày</h5>
          <div class="ibox-tools">
            <form method="get" action="{{ route('sanpham.statistics.page') }}" class="form-inline">
              <label class="mr-2">Khoảng:</label>
              <select name="range" class="form-control input-sm" onchange="this.form.submit()">
                <option value="7" {{ ($range ?? 30)==7?'selected':'' }}>7 ngày</option>
                <option value="30" {{ ($range ?? 30)==30?'selected':'' }}>30 ngày</option>
                <option value="90" {{ ($range ?? 30)==90?'selected':'' }}>90 ngày</option>
              </select>
            </form>
          </div>
        </div>
        <div class="ibox-content"><canvas id="prodLineChart" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox"><div class="ibox-title"><h5>Tỷ lệ trạng thái</h5></div><div class="ibox-content"><canvas id="prodDonutChart" height="300"></canvas></div></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart==='undefined') return;
  const lc = document.getElementById('prodLineChart');
  if (lc){
    new Chart(lc,{type:'line',data:{labels:@json($labels),datasets:[{label:'Sản phẩm mới',data:@json($data),borderColor:'#1ab394',backgroundColor:'rgba(26,179,148,0.15)',tension:.3,fill:true}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  }
  const dc = document.getElementById('prodDonutChart');
  if (dc){
    const active={{ (int)$stats['active'] }}; const inactive={{ (int)$stats['inactive'] }};
    new Chart(dc,{type:'doughnut',data:{labels:['Đang KD','Ngừng KD'],datasets:[{data:[active,inactive],backgroundColor:['#1ab394','#f8ac59']}]} , options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
  }
})();
</script>
@endpush

