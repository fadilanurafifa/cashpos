@extends('admin.layouts.base')

@section('title', 'Dashboard Chef')

@section('content')
<div class="container mt-4">
    <h1 class="h3 text-gray-800">
        <i class="fas fa-chart-line"></i> Dashboard Chef
    </h1>
    <p class="text-muted">Menampilkan ringkasan pesanan aktif, status pesanan, dan informasi penting lainnya.</p>
    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @foreach(Auth::user()->unreadNotifications as $notification)
            <div class="toast align-items-center text-white bg-success border-0 fade show custom-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-bell me-2"></i> {{ $notification->data['message'] }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endforeach
    </div>
    

    <div class="row">
        <!-- Card Jumlah Pesanan Selesai -->
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pesanan Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedOrdersCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Rata-rata Waktu Penyelesaian -->
        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata Penyelesaian
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $averageCompletionTime ? round($averageCompletionTime, 2) . ' menit' : 'Belum ada data' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Menu Terlaris -->
        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menu Terlaris
                            </div>
                            <ul class="list-group">
                                @foreach($topMenus as $menu)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $menu->produk->nama_produk ?? 'Produk Tidak Diketahui' }}
                                        <span class="badge badge-primary badge-pill">{{ $menu->total }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="notifikasi-container"></div>
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
@endpush