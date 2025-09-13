@extends('backend.layout')
@section('title','Thống kê Voucher')
@section('content')
<div class="wrapper wrapper-content voucher-statistics-page">
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>Thống kê Voucher</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="active"><strong>Thống kê Voucher</strong></li>
      </ol>
    </div>
  </div>

  <div class="row mt10">
    <div class="col-lg-3">
      <div class="ibox">
        <div class="ibox-content">
          <h5 class="m-b-none">Tổng voucher</h5>
          <h2 class="no-margins text-primary">{{ number_format($statistics['total'] ?? 0) }}</h2>
          <small>Tất cả voucher</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox">
        <div class="ibox-content">
          <h5 class="m-b-none">Đang hoạt động</h5>
          <h2 class="no-margins text-success">{{ number_format($statistics['active'] ?? 0) }}</h2>
          <small>Tỉ lệ: {{ ($statistics['total'] ?? 0) ? number_format(($statistics['active'] ?? 0)*100/($statistics['total'] ?? 1),1) : 0 }}%</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox">
        <div class="ibox-content">
          <h5 class="m-b-none">Hết hạn</h5>
          <h2 class="no-margins text-danger">{{ number_format($statistics['expired'] ?? 0) }}</h2>
          <small>Tỉ lệ: {{ ($statistics['total'] ?? 0) ? number_format(($statistics['expired'] ?? 0)*100/($statistics['total'] ?? 1),1) : 0 }}%</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox">
        <div class="ibox-content">
          <h5 class="m-b-none">Sắp hết hạn (7 ngày)</h5>
          <h2 class="no-margins text-warning">{{ number_format(($expiringSoon ?? collect([]))->count()) }}</h2>
          <small>Trong 7 ngày tới</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-9">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Voucher tạo theo ngày</h5>
          <div class="ibox-tools">
            <form method="get" action="{{ route('admin.vouchers.statistics') }}" class="form-inline">
              <label class="mr-2">Khoảng:</label>
              <select name="range" class="form-control input-sm" onchange="this.form.submit()">
                <option value="7" {{ (request('range',30)==7)?'selected':'' }}>7 ngày</option>
                <option value="30" {{ (request('range',30)==30)?'selected':'' }}>30 ngày</option>
                <option value="90" {{ (request('range',30)==90)?'selected':'' }}>90 ngày</option>
              </select>
            </form>
          </div>
        </div>
        <div class="ibox-content">
          <canvas id="voucherLineChart" height="160"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Tỷ lệ trạng thái</h5>
        </div>
        <div class="ibox-content">
          <canvas id="voucherDonutChart" height="300"></canvas>
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
  
  // Line Chart
  const lc = document.getElementById('voucherLineChart');
  if (lc){
    new Chart(lc, {
      type: 'line',
      data: {
        labels: @json($labels ?? []),
        datasets: [{
          label: 'Voucher mới',
          data: @json($data ?? []),
          borderColor: '#1ab394',
          backgroundColor: 'rgba(26,179,148,0.15)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#1ab394',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0,0,0,0.1)'
            }
          },
          x: {
            grid: {
              color: 'rgba(0,0,0,0.1)'
            }
          }
        }
      }
    });
  }
  
  // Donut Chart
  const dc = document.getElementById('voucherDonutChart');
  if (dc){
    const active = {{ (int)($statistics['active'] ?? 0) }};
    const expired = {{ (int)($statistics['expired'] ?? 0) }};
    
    new Chart(dc, {
      type: 'doughnut',
      data: {
        labels: ['Hoạt động', 'Hết hạn'],
        datasets: [{
          data: [active, expired],
          backgroundColor: ['#1ab394', '#ed5565'],
          borderWidth: 0,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }
    });
  }
})();
</script>
@endpush

