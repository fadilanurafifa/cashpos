@extends('admin.layouts.base')

@section('title', 'Laporan Penjualan')

@section('content')
@push('style')
    <style>
        @media print {
    body * {
        visibility: hidden;
    }
    #laporanArea, #laporanArea * {
        visibility: visible;
    }
    #laporanArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .btn {
        display: none !important; /* Menyembunyikan tombol saat cetak */
    }
    }
        /* Pastikan parent container flex untuk mengatur posisi */
.dataTables_wrapper .row:first-child {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

/* Form Show tetap di kiri */
.dataTables_length {
    flex: none !important;
}

/* Pastikan form search tetap di kanan */
.dataTables_filter {
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
}

/* Membuat teks "Cari" sejajar di sebelah kiri input */
.dataTables_filter label {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important; /* Jarak antara teks dan input */
    white-space: nowrap; /* Mencegah teks turun ke bawah */
}

/* Menyesuaikan ukuran input */
.dataTables_filter input {
    width: 200px !important;
    padding: 5px !important;
    border-radius: 5px !important;
    border: 1px solid #ccc !important;
}

    </style>
@endpush
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-chart-bar"></i> Laporan Penjualan
            </h1>
            <p class="text-muted">
                <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="#" class="text-custom text-decoration-none">Laporan Penjualan</a>
            </p>  
        </div>

        <div class="d-flex justify-content-between align-items-center" style="gap: 15px;">
        <!-- Tombol Download PDF -->
        <button type="button" class="btn btn-warning btn-sm" onclick="exportExcel()">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button type="button" class="btn btn-danger btn-sm" onclick="cetakLaporan()">
            <i class="fas fa-file-pdf"></i> Cetak Laporan
        </button>
        </div>
        
    </div>   
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.penjualan') }}" style="margin-bottom: 20px;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <label class="fw-bold mb-0" style="font-size: 14px;">Filter Kategori:</label>
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <select name="kategori_id" class="form-control custom-select">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm d-flex align-items-center" 
                        style="background-color: #89AC46; border-color: #789C40; color: white;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>                   
                </div>
            </form>
            
            <!-- Tabel Laporan dengan DataTables -->
            <div class="card table-container">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanTable" class="table table-bordered">
                        <thead class="thead-light">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Stok Awal</th>
                            <th>Terjual</th>
                            <th>Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $produk)
                            @php
                                $stok_awal = 100; // Stok awal per bulan
                                $terjual = $produk->detailPenjualan
                                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                                    ->sum('jumlah');

                                // Perhitungan keuntungan (total terjual * harga produk)
                                $keuntungan = $terjual * $produk->harga;
                            @endphp
                            <tr>
                                <td>{{ $produk->nama_produk }}</td>
                                <td>{{ $stok_awal }}</td>
                                <td>{{ $terjual }}</td>
                                <td>Rp{{ number_format($keuntungan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')

<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
let table = $('#laporanTable').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false
});

$(document).ready(function() {
var table = $('#yourTableID').DataTable();

// Menambahkan placeholder ke input pencarian bawaan DataTables
$('.dataTables_filter input').attr('placeholder', 'Cari data laporan...');
});
});
    </script>

<script> 

    // Fungsi Cetak Laporan
    function cetakLaporan() {
        let printContents = document.getElementById("laporanArea").innerHTML;
        let originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // Refresh halaman setelah cetak
    }
</script>

<!-- CSS untuk mode cetak -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #laporanArea, #laporanArea * {
            visibility: visible;
        }
        #laporanArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn {
            display: none !important;
        }
    }
</style>

<script>
    function cetakLaporan() {
        fetch("{{ route('cetak.pdf', ['kategori_id' => request()->kategori_id]) }}")
            .then(response => response.text()) // Ambil HTML dari server
            .then(html => {
                let printWindow = window.open("", "_blank");
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.onload = function () {
                    printWindow.print(); // Langsung cetak
                    setTimeout(() => printWindow.close(), 500); // Tutup setelah selesai
                };
            })
            .catch(error => console.error("Gagal memuat laporan:", error));
    }
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $("form").on("submit", function (event) {
            event.preventDefault(); // Mencegah pengiriman form langsung
            
            // Tampilkan SweetAlert loading selama 3 detik
            Swal.fire({
                title: "Sedang Memproses...",
                text: "Mohon tunggu sebentar!",
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 1500, // 2 detik
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Setelah 3 detik, kirimkan form secara normal
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    });
</script>
<script>
    function exportExcel() {
    window.location.href = "{{ route('admin.laporan.exportExcel', ['kategori_id' => request()->kategori_id]) }}";
}
</script>
@endpush
