@extends('backend.layout')
@section('title','Thống kê User')
@section('content')
<div class="wrapper wrapper-content">
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>Thống kê User</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="active"><strong>Thống kê User</strong></li>
      </ol>
    </div>
  </div>

  <div class="row mt10">
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Tổng user</h5><h2 class="no-margins text-primary">{{ number_format($stats['total_users']) }}</h2><small>Tất cả user</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Đang hoạt động</h5><h2 class="no-margins text-success">{{ number_format($stats['active_users']) }}</h2><small>User active</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Ngừng hoạt động</h5><h2 class="no-margins text-warning">{{ number_format($stats['inactive_users']) }}</h2><small>User inactive</small></div></div></div>
    <div class="col-lg-3"><div class="ibox"><div class="ibox-content"><h5 class="m-b-none">Admin</h5><h2 class="no-margins text-danger">{{ number_format($stats['admin_users']) }}</h2><small>User admin</small></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-9">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Đăng ký theo ngày</h5>
          <div class="ibox-tools">
            <form method="get" action="{{ route('user.statistics') }}" class="form-inline">
              <label class="mr-2">Khoảng:</label>
              <select name="range" class="form-control input-sm" onchange="this.form.submit()">
                <option value="7" {{ $range==7?'selected':'' }}>7 ngày</option>
                <option value="30" {{ $range==30?'selected':'' }}>30 ngày</option>
                <option value="90" {{ $range==90?'selected':'' }}>90 ngày</option>
              </select>
            </form>
          </div>
        </div>
        <div class="ibox-content"><canvas id="userLineChart" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox"><div class="ibox-title"><h5>Tỷ lệ trạng thái</h5></div><div class="ibox-content"><canvas id="userDonutChart" height="300"></canvas></div></div>
      <div class="ibox"><div class="ibox-title"><h5>User mới</h5></div><div class="ibox-content" style="max-height:280px;overflow:auto;">
        <ul class="list-unstyled mb-0">
          @foreach($recentUsers as $u)
            <li class="mb-2 d-flex justify-content-between"><span>{{ $u->name }}</span><small class="text-muted">{{ \Carbon\Carbon::parse($u->created_at)->format('d/m') }}</small></li>
          @endforeach
        </ul>
      </div></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart==='undefined') return;
  const lc = document.getElementById('userLineChart');
  if (lc){
    new Chart(lc,{type:'line',data:{labels:@json($chart['labels'] ?? []),datasets:[{label:'Đăng ký',data:@json($chart['data'] ?? []),borderColor:'#1ab394',backgroundColor:'rgba(26,179,148,0.15)',tension:.3,fill:true}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  }
  const dc = document.getElementById('userDonutChart');
  if (dc){
    new Chart(dc,{type:'doughnut',data:{labels:['Active','Inactive','Admin'],datasets:[{data:[{{ (int)$stats['active_users'] }},{{ (int)$stats['inactive_users'] }},{{ (int)$stats['admin_users'] }}],backgroundColor:['#1ab394','#f8ac59','#ed5565']}]} , options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
  }
})();
</script>
@endpush

