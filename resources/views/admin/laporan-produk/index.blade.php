@extends('admin.layouts.base')

@section('title', 'Laporan Data Produk')

@push('style')
<!-- Tambahkan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<style>
    .table-container {
        padding: 20px;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    /* Styling input search */
    .dataTables_filter input {
        border: 1px solid #ccc !important;
        border-radius: 5px !important;
        padding: 8px 12px !important;
        outline: none !important;
        box-shadow: none !important;
        transition: all 0.3s ease-in-out;
    }

    /* Hilangkan border saat input aktif */
    .dataTables_filter input:focus {
        border-color: #aaa !important;
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
        <!-- Judul & Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-clipboard-list"></i> Manajemen Produk
                </h1>
                <p class="text-muted">
                    <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                    <a href="#" class="text-custom text-decoration-none">Manajemen Produk</a>
                </p>                
            </div>
    
            <!-- Tombol Download PDF -->
            <a href="{{ route('laporan.produk.pdf') }}" target="_blank" onclick="window.open(this.href, '_blank'); return false;" class="btn btn-danger btn-sm">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>                 
        </div>    

    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table id="produkTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Kategori ID</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Tanggal Dibuat</th>
                            <th>Tanggal Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->kategori ? $item->kategori->nama_kategori : '-' }}</td> 
                            <td>{{ $item->nama_produk }}</td>
                            <td>Rp {{ number_format($item->harga, 2, ',', '.') }}</td>
                            <td>{{ $item->stok }}</td>
                            <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $item->updated_at->format('d-m-Y H:i') }}</td>
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
<!-- Tambahkan Script DataTables -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#produkTable').DataTable({
        "lengthChange": false, // Menghilangkan dropdown "Show entries"
        "language": {
            "search": "Cari:", // Mengubah label search menjadi "Cari:"
            "searchPlaceholder": "Masukkan kata kunci..." // Menambahkan placeholder
        }
    });
});
</script>
@endpush
