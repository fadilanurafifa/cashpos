@extends('admin.layouts.base')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 text-gray-800">
            Dashboard
        </h1>
        <p class="text-muted">
            <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
            <a href="#" class="text-custom text-decoration-none">Pages</a>
        </p>                
    </div>
</div>    
<div class="row">
    <!-- Member (Customer) Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Member (Customer)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalPelanggan }} Member
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>            
        </div>
    </div>

    <!-- Transaksi Penjualan Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Penjualan
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    {{ number_format($persentase, 0) }}%
                                </div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>            
        </div>
    </div>

    <!-- Income (Pemasukan) Card -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Income (Pemasukan)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">{{ number_format($incomePercentage, 2) }}% dari target</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

<!-- Chart Section -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark">STATISTIKA PENJUALAN</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="height: 400px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');

    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [
                {
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($transactionsByDay) !!},
                    borderColor: 'rgba(0, 31, 63, 1)',
                    backgroundColor: 'rgba(0, 31, 63, 0.5)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(0, 31, 63, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Total Pendapatan',
                    data: {!! json_encode($incomeByDay) !!},
                    borderColor: 'rgba(0, 150, 0, 1)',
                    backgroundColor: 'rgba(0, 150, 0, 0.5)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(0, 150, 0, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: "#001f3f",
                        font: { size: 14, weight: "bold" }
                    }
                },
                tooltip: {
                    backgroundColor: "rgba(0, 0, 0, 0.8)"
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: "#001f3f",
                        font: { size: 12, weight: "bold" }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 31, 63, 0.2)'
                    },
                    ticks: {
                        color: "#001f3f",
                        font: { size: 12, weight: "bold" }
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
</script>
@endpush
