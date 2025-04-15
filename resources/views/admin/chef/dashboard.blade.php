@extends('admin.layouts.base')

@section('title', 'Dashboard Chef')

@section('content')
<div class="container mt-4">
    {{-- <h1 class="h3 text-gray-800">
        <i class="fas fa-chart-line"></i> Dashboard Chef
    </h1> --}}
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-clipboard-list"></i> Dashboard Chef
            </h1>
            <p class="text-muted">
                <a href="{{ route('chef.dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="#" class="text-custom text-decoration-none">Pages</a>
            </p>                
        </div>
    </div>            

    {{-- <p class="text-muted">Menampilkan ringkasan pesanan aktif, status pesanan, dan informasi penting lainnya.</p> --}}

    <div class="row gx-4 gy-4">
        <!-- Box Pesanan Selesai -->
        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pesanan Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedOrdersCount }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

       <!-- Box Jumlah Pesanan Aktif -->
        <div class="col-md-6">
            <div class="card border-left-info shadow h-100 py-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah Pesanan Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeOrdersCount > 0 ? $activeOrdersCount . ' Pesanan sedang diproses' : 'Tidak ada pesanan aktif' }}
                            </div>
                        </div>
                        <i class="fas fa-concierge-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart dan Menu Terlaris -->
    <div class="row gx-4 gy-4 mt-3" style="margin-bottom: 30px;">
        <!-- Chart di sebelah kiri -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <canvas id="menuChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Box Menu Terlaris di sebelah kanan -->
        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-3 d-flex flex-column">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        5 Menu Terlaris
                    </div>
                    <ul class="list-group overflow-auto" style="max-height: 250px;">
                        @foreach($topMenus as $menu)
                            <li class="list-group-item d-flex justify-content-between align-items-center menu-item"
                                data-menu="{{ $menu->produk->nama_produk ?? 'Produk Tidak Diketahui' }}"
                                data-total="{{ $menu->total }}">
                                {{ $menu->produk->nama_produk ?? 'Produk Tidak Diketahui' }}
                                <span class="badge badge-primary badge-pill">{{ $menu->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    setInterval(() => {
        $.get("{{ route('chef.checkNewOrders') }}", function(response) {
            if (response.new_order) {
                showToast(response.message);
            }
        });
    }, 1000);

    function showToast(message) {
        let toastContainer = document.querySelector(".toast-container");

        let toastHTML = `
            <div class="toast align-items-center text-white bg-success border-0 fade show custom-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-bell me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.innerHTML += toastHTML;

        setTimeout(() => {
            let toastElement = toastContainer.lastElementChild;
            if (toastElement) {
                toastElement.classList.remove("show");
                setTimeout(() => toastElement.remove(), 100);
            }
        }, 1000);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   var ctx = document.getElementById('menuChart').getContext('2d');

var initialLabels = {!! json_encode($menuNames) !!};
var initialData = {!! json_encode($menuOrders) !!};

var menuChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: initialLabels,
        datasets: [{
            label: 'Jumlah Pesanan',
            data: initialData,
            borderColor: '#007bff', // Biru elegan
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            pointBackgroundColor: '#007bff',
            pointRadius: 6,
            pointHoverRadius: 8,
            tension: 0.4, // Membuat garis melengkung (smooth)
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: '#333',
                    font: {
                        size: 14
                    }
                }
            },
            title: {
                display: true,
                text: 'Statistik Pesanan Menu',
                font: {
                    size: 18,
                    weight: 'bold'
                },
                color: '#444',
                padding: {
                    top: 10,
                    bottom: 20
                }
            },
            tooltip: {
                backgroundColor: '#343a40',
                titleColor: '#fff',
                bodyColor: '#fff',
                padding: 10,
                cornerRadius: 6
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#333'
                },
                grid: {
                    display: true,
                    color: '#e0e0e0'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#333'
                },
                grid: {
                    display: true,
                    color: '#e0e0e0'
                }
            }
        }
    }
});

</script>
<script>
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function () {
            var selectedMenu = this.getAttribute('data-menu');
            var totalOrders = this.getAttribute('data-total');

            var index = initialLabels.indexOf(selectedMenu);

            if (index !== -1) {
                var newDataset = {
                    label: ' ' + selectedMenu + '',
                    data: initialData.map((value, i) => i === index ? value : null),
                    borderColor: '#00008B', // biru dongker
                    backgroundColor: 'rgba(0, 0, 139, 0.1)', // biru dongker transparan
                    pointBackgroundColor: '#00008B',
                    pointRadius: 10,
                    borderWidth: 2,
                    fill: false
                };

                menuChart.data.datasets = [
                    {
                        label: 'Jumlah Pesanan',
                        data: initialData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        pointBackgroundColor: '#007bff',
                        pointRadius: 6,
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true
                    },
                    newDataset
                ];

                // Update judul chart
                menuChart.options.plugins.title.text = 'Statistik Pesanan - ' + selectedMenu;

                menuChart.update();
            }
        });
    });
</script>

@endpush
