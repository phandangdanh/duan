@extends('backend.layout')
@section('title','Thống kê Danh mục')
@section('content')
<div class="wrapper wrapper-content">
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>Thống kê Danh mục</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="active"><strong>Thống kê Danh mục</strong></li>
      </ol>
    </div>
  </div>

  @php
    $total = (int)($stats['total_categories'] ?? 0);
    $pct = function($v,$t){ return $t>0 ? number_format($v*100/$t,1) : 0; };
  @endphp

  <div class="row mt10">
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Tổng danh mục</h5><h2 class="no-margins text-primary">{{ number_format($stats['total_categories']) }}</h2><small>Tất cả danh mục</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Đang hoạt động</h5><h2 class="no-margins text-success">{{ number_format($stats['active_categories']) }}</h2><small>Tỉ lệ: {{ $pct($stats['active_categories'],$total) }}%</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Ngừng hoạt động</h5><h2 class="no-margins text-warning">{{ number_format($stats['inactive_categories']) }}</h2><small>Tỉ lệ: {{ $pct($stats['inactive_categories'],$total) }}%</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Danh mục gốc</h5><h2 class="no-margins text-info">{{ number_format($stats['root_categories']) }}</h2><small>Danh mục cha</small></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-9">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Danh mục mới theo ngày</h5>
          <div class="ibox-tools">
            <form method="get" action="{{ route('danhmuc.statistics') }}" class="form-inline">
              <label class="mr-2">Khoảng:</label>
              <select name="range" class="form-control input-sm" onchange="this.form.submit()">
                <option value="7" {{ $range==7?'selected':'' }}>7 ngày</option>
                <option value="30" {{ $range==30?'selected':'' }}>30 ngày</option>
                <option value="90" {{ $range==90?'selected':'' }}>90 ngày</option>
              </select>
            </form>
          </div>
        </div>
        <div class="ibox-content"><canvas id="catLineChart" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox"><div class="ibox-title"><h5>Tỷ lệ trạng thái</h5></div><div class="ibox-content"><canvas id="catDonutChart" height="300"></canvas></div></div>
      <div class="ibox"><div class="ibox-title"><h5>Top danh mục theo số sản phẩm</h5></div>
        <div class="ibox-content" style="max-height:280px;overflow:auto;">
          <ul class="list-unstyled mb-0">
            @foreach($topCategories as $cat)
              <li class="mb-2 d-flex justify-content-between">
                <span>{{ $cat->name }}</span>
                <small class="text-muted">{{ $cat->total_products }} SP</small>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart==='undefined') return;
  const lc = document.getElementById('catLineChart');
  if (lc){
    new Chart(lc,{type:'line',data:{labels:@json($labels),datasets:[{label:'Danh mục mới',data:@json($data),borderColor:'#1ab394',backgroundColor:'rgba(26,179,148,0.15)',tension:.3,fill:true}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  }
  const dc = document.getElementById('catDonutChart');
  if (dc){
    new Chart(dc,{type:'doughnut',data:{labels:['Active','Inactive'],datasets:[{data:[{{ (int)$stats['active_categories'] }},{{ (int)$stats['inactive_categories'] }}],backgroundColor:['#1ab394','#f8ac59']}]} , options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
  }
})();
</script>
@endpush

