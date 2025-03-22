@extends('admin.layouts.base')

@section('title', 'Laporan Transaksi')

@push('style')
<style>
   .btn-custom {
    background-color: #89AC46; /* Warna hijau sesuai permintaan */
    border: none;
    color: white;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    }

    .btn-custom:hover {
        background-color: #89AC46 !important; /* Tetap hijau saat hover */
        color: white !important;
    }
    .table-container {
        padding: 20px;
    }
    .table {
        font-size: 16px; 
    }
    .table th, .table td {
        padding: 6px; 
    }
    .modal-dialog {
        max-width: 400px; 
    }
    .modal-content {
        padding: 8px; 
    }
    .modal-body {
        padding: 10px;
    }
    .modal-footer {
        padding: 5px;
    }
    .hidden {
        display: none;
    }
    .struk-container {
    font-family: 'Courier New', Courier, monospace;
    font-size: 11px;
    text-align: center;
    width: 58mm;
    padding: 5px;
    }

    .title {
        font-size: 13px;
        font-weight: bold;
    }

    .line {
        border-top: 1px dashed black;
        margin: 5px 0;
    }

    table {
        width: 100%;
        text-align: left;
        font-size: 10px;
    }

    .right {
        text-align: right;
    }

    .bold {
        font-weight: bold;
    }
</style>
@endpush
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-receipt"></i> Laporan Transaksi
            </h1>
            <p class="text-muted">
                <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="#" class="text-custom text-decoration-none">Laporan Transaksi</a>
            </p>                
        </div>
    </div>       
    @php
    $totalIncome = $transaksi->where('status_pembayaran', 'lunas')->sum('total_bayar');
    @endphp
    
    <div class="d-flex justify-content-between align-items-center mb-4">
       <!-- Button Filter (Kiri) -->
        <div class="d-flex gap-3" style="margin-top: -30px;">
            <button class="btn btn-custom d-flex align-items-center" onclick="filterTable('all')">
                <i class="fas fa-list me-2"></i> Semua Transaksi
            </button>
            <button class="btn btn-custom d-flex align-items-center" onclick="filterTable('member')">
                <i class="fas fa-user-check me-2"></i> Pelanggan Member
            </button>
            <button class="btn btn-custom d-flex align-items-center" onclick="filterTable('biasa')">
                <i class="fas fa-user me-2"></i> Pelanggan Biasa
            </button>
        </div>
        <!-- Box Total Income (Kanan) -->
        <div class="d-flex align-items-center">
            <i class="fas fa-coins me-3 fa-2x"></i>

            <div>
                <h6 class="mb-1 text-muted" style="font-size: 14px;">Total Pemasukan :</h6>
                <h4 class="fw-bold m-0 text-dark">Rp.{{ number_format($totalIncome, 0, ',', '.') }}</h4>
            </div>
        </div>
        
    </div>    
    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaksiTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>ID Penjualan</th>
                            <th>Nama Pelanggan</th>
                            <th>Tipe Pelanggan</th> <!-- Tambahkan ini -->
                            <th>Metode Pembayaran</th>
                            <th>Total Harga</th>
                            <th>Tanggal Transaksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>                                 
                    <tbody>
                        @foreach($detailTransaksi as $penjualan_id => $details)
                        @php $penjualan = $transaksi[$penjualan_id] ?? null; @endphp
                        @if($penjualan)
                        <tr class="transaksi-row" data-type="{{ $penjualan->pelanggan ? 'member' : 'biasa' }}">
                            <td class="text-center align-middle">{{ $penjualan_id }}</td>
                            <td class="align-middle">
                                {{ $penjualan->pelanggan ? $penjualan->pelanggan->nama : 'Pelanggan Biasa' }}
                            </td>
                            <td class="align-middle">
                                <span style="font-weight: bold; color: {{ $penjualan->pelanggan ? 'green' : 'red' }};">
                                    {{ $penjualan->pelanggan ? 'Member' : 'Non-Member' }}
                                </span>
                            </td>                            
                            <td class="align-middle">Cash</td>
                            <td class="align-middle">Rp.{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</td>
                            <td class="align-middle">{{ $penjualan->created_at->format('d-m-Y H:i') }}</td>
                            <td class="align-middle">
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $penjualan_id }}">
                                    <i class="fas fa-eye"></i> 
                                </button>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($detailTransaksi as $penjualan_id => $details)
@php $penjualan = $transaksi[$penjualan_id] ?? null; @endphp
@if($penjualan)
<div class="modal fade" id="modalDetail{{ $penjualan_id }}" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content" style="padding: 10px;">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $detail)
                        <tr>
                            <td>{{ optional($detail->produk)->nama_produk ?? 'Produk Tidak Ditemukan' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="mt-2" style="font-size: 14px; background-color: #d1ecf1; padding: 6px 12px; border-radius: 5px; color: #0c5460; font-weight: bold;">
                    <strong>Status Pembayaran :</strong> {{ ucfirst($penjualan->status_pembayaran) }}
                </p>                              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <!-- Tombol untuk menampilkan struk -->
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalStruk{{ $penjualan_id }}">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- modal struk --}}
@foreach($transaksi as $penjualan)
<div class="modal fade" id="modalStruk{{ $penjualan->id }}" tabindex="-1" aria-labelledby="modalStrukLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 350px;">
        <div class="modal-content">
            <div class="modal-body">
                <div id="struk{{ $penjualan->id }}" class="struk-container">
                    <div class="text-center">
                        <p class="title">Kasir Caffe</p>
                        <p>Jl. Merdeka Belajar No.12<br>Bandung - Jawa Barat</p>
                        <div class="line"></div>
                        <p class="bold">No Faktur: {{ $penjualan->id }}</p>
                        <p class="bold">Tanggal: {{ $penjualan->created_at->format('d-m-Y H:i') }}</p>
                        <div class="line"></div>
                    </div>

                    <table style="width: 100%; font-size: 12px;">
                        <tbody>
                            @foreach($penjualan->detailTransaksi ?? [] as $detail)
                            <tr>
                                <td>{{ $detail->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</td>
                                <td class="right">{{ $detail->jumlah }} x Rp {{ number_format($detail->sub_total / max($detail->jumlah, 1), 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="line"></div>
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="right"><strong>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Bayar</td>
                            <td class="right">Rp {{ number_format($penjualan->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Kembalian</td>
                            <td class="right">Rp {{ number_format(max(($penjualan->jumlah_bayar ?? 0) - $penjualan->total_harga, 0), 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="line"></div>
                    <p>Terima Kasih atas kunjungan Anda!<br>~ Kasir Caffe ~</p>
                </div>

                <!-- Tombol Cetak -->
                <button class="btn btn-success btn-sm w-100 mt-2" onclick="printStruk('struk{{ $penjualan->id }}')">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach


{{-- @foreach($detailTransaksi as $penjualan_id => $details)
@php $penjualan = $transaksi[$penjualan_id] ?? null; @endphp
@if($penjualan)
<div class="modal fade" id="modalDetail{{ $penjualan_id }}" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content" style="padding: 10px;">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $detail)
                        <tr>
                            <td>{{ optional($detail->produk)->nama_produk ?? 'Produk Tidak Ditemukan' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="mt-2" style="font-size: 14px; background-color: #d1ecf1; padding: 6px 12px; border-radius: 5px; color: #0c5460; font-weight: bold;">
                    <strong>Status Pembayaran :</strong> {{ ucfirst($penjualan->status_pembayaran) }}
                </p>                              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach --}}
@endsection

@push('script')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#transaksiTable').DataTable({
        "language": {
            "search": "Cari Nama:", // Teks tetap di samping kiri input
            "searchPlaceholder": "Cari Nama...", // Placeholder dalam input
            "zeroRecords": "Tidak ada transaksi ditemukan"
        }
    });

    // Pastikan teks "Cari Nama:" tetap sejajar dengan input
    setTimeout(function() {
        $('.dataTables_filter').css({
            "display": "flex",  // Gunakan flexbox agar sejajar
            "align-items": "center", // Posisikan secara vertikal tengah
            "justify-content": "flex-end", // Posisi ke kanan container
            "width": "100%" // Gunakan lebar penuh agar mentok ke kanan
        });

        $('.dataTables_filter label').css({
            "margin-right": "8px", // Beri jarak antara teks "Cari Nama:" dan input
            "white-space": "nowrap", // Hindari teks turun ke bawah
        });

        $('.dataTables_filter input').css({
            "width": "200px", // Sesuaikan ukuran input
            "padding": "5px", // Tambahkan padding agar lebih enak dilihat
            "border-radius": "5px", // Sedikit rounded pada input
            "border": "1px solid #ccc" // Tambahkan border agar lebih jelas
        });
    }, 100);
});

function filterTable(type) {
    if (type === 'all') {
        $('.transaksi-row').show();
    } else {
        $('.transaksi-row').hide();
        $('.transaksi-row[data-type="' + type + '"]').show();
    }
}
</script>
<script>
    function printStruk(id) {
        var content = document.getElementById(id).innerHTML;
        var originalContent = document.body.innerHTML;
        
        document.body.innerHTML = content;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // Reload halaman setelah cetak agar tetap normal
    }
</script>
{{-- cetak struk --}}
<script>
    function printStruk(id) {
        var content = document.getElementById(id).innerHTML;
        var printWindow = window.open('', '', 'width=350,height=500');
        printWindow.document.write('<html><head><title>Struk Pembayaran</title>');
        printWindow.document.write('<style>');
        printWindow.document.write(`
            body { font-family: 'Courier New', Courier, monospace; font-size: 11px; align-items: center; }
            .title { font-size: 13px; font-weight: bold; }
            .line { border-top: 1px dashed black; margin: 5px 0; }
            table { width: 100%; text-align: left; font-size: 10px; }
            .right { text-align: right; }
            .bold { font-weight: bold; }
            @media print {
                @page { size: 58mm auto; margin: 0; }
                body { width: 58mm; height: auto; }
            }
        `);
        printWindow.document.write('</style></head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }
    </script>
    
@endpush

