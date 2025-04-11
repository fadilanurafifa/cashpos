@extends('admin.layouts.base')

@section('title', 'Laporan Transaksi')

@section('content')
@push('style')
<style>
   .btn-custom {
    background-color: #89AC46;
    border: none;
    color: white;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    }
    .btn-custom:hover, 
    .btn-custom:focus, 
    .btn-custom:active {
        background-color: #89AC46 !important; 
        color: white !important;
        box-shadow: none !important; 
    }
    .dropdown-menu .dropdown-item:hover, 
    .dropdown-menu .dropdown-item:focus {
        background-color: transparent !important; 
        color: inherit !important;
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
.input-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }

    .btn-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }

    .form-label-sm {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .filter-wrapper {
        row-gap: 1rem;
    }

    .filter-transaksi {
        margin-right: 30px; /* Atur jarak antara dropdown dan tanggal */
    }

    .reset-link {
        cursor: pointer;
        margin-top: 6px;
        font-size: 0.85rem;
        color: #dc3545; /* merah */
        transition: color 0.2s;
    }

    .reset-link:hover {
        color: #a71d2a;
        text-decoration: underline;
    }


</style>
@endpush

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
        <div class="d-flex gap-2">
            <a href="{{ route('export.transaksi.excel') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('export.transaksi.pdf') }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i> Cetak Laporan
            </a>            
        </div>
    
    </div>       
    @php
    $totalIncome = $transaksi->where('status_pembayaran', 'lunas')->sum('total_bayar');
    @endphp
    
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="row align-items-end mb-3 filter-wrapper">
            <!-- Filter Transaksi Dropdown -->
            <div class="col-md-3 filter-transaksi">
                <label class="form-label-sm invisible">.</label> <!-- untuk sejajarkan -->
                <div class="dropdown">
                    <button class="btn btn-custom btn-sm dropdown-toggle d-flex align-items-center" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-2"></i> Filter Transaksi
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#" onclick="filterTable('all')"><i class="fas fa-list me-2"></i> Semua Transaksi</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="#" onclick="filterTable('member')"><i class="fas fa-user-check me-2"></i> Pelanggan Member</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="#" onclick="filterTable('biasa')"><i class="fas fa-user me-2"></i> Pelanggan Biasa</a></li>
                    </ul>
                </div>
            </div>
        
            <!-- Filter Tanggal -->
            <div class="col-md-3">
                <label for="startDate" class="form-label-sm">Dari Tanggal :</label>
                <input type="date" id="startDate" class="form-control form-control-sm">
            </div>
        
            <div class="col-md-3">
                <label for="endDate" class="form-label-sm">Sampai Tanggal :</label>
                <input type="date" id="endDate" class="form-control form-control-sm">
            </div>
        
            <!-- Reset -->
            <div class="col-md-2 d-flex flex-column justify-content-end align-items-start">
                <span class="small text-danger reset-link" onclick="resetDateFilter()">
                    <i class="fas fa-times-circle me-1"></i> Reset
                </span>
            </div>
        </div>
        
        {{-- <div class="d-flex align-items-center">
            <i class="fas fa-coins me-3 fa-2x"></i>

            <div>
                <h6 class="mb-1 text-muted" style="font-size: 14px;">Total Keuntungan :</h6>
                <h4 class="fw-bold m-0 text-dark">Rp.{{ number_format($totalIncome, 0, ',', '.') }}</h4>
            </div>
            
        </div> --}}
        
    </div>    
    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaksiTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>ID Penjualan</th>
                            <th>Slot Kasir</th>
                            <th>Nama Pelanggan</th>
                            <th>Tipe Pelanggan</th> 
                            {{-- <th>Metode Pembayaran</th> --}}
                            <th>Total Harga</th>
                            <th>Tanggal Transaksi</th>
                            {{-- <th>Nama Kasir</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>                                 
                    <tbody>
                        @foreach($detailTransaksi as $penjualan_id => $details)
                        @php $penjualan = $transaksi[$penjualan_id] ?? null; @endphp
                        @if($penjualan)
                        <tr class="transaksi-row" data-type="{{ $penjualan->pelanggan ? 'member' : 'biasa' }}">
                            <td class="text-center align-middle">{{ $penjualan_id }}</td>
                            <td class="align-middle">{{ $penjualan->kasir->slot_kasir ?? '-' }}</td>
                            <td class="align-middle">
                                {{ $penjualan->pelanggan ? $penjualan->pelanggan->nama : 'Pelanggan Biasa' }}
                            </td>
                            <td class="align-middle">
                                <span style="font-weight: bold; color: {{ $penjualan->pelanggan ? 'green' : 'red' }};">
                                    {{ $penjualan->pelanggan ? 'Member' : 'Non-Member' }}
                                </span>
                            </td>                            
                            {{-- <td class="align-middle">Cash</td> --}}
                            <td class="align-middle">Rp.{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</td>
                            <td class="align-middle">{{ $penjualan->created_at->format('d-m-Y H:i') }}</td>
                            {{-- <td class="align-middle">{{ $penjualan->kasir_nama ?? '-' }}</td> --}}
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
            
                <!-- Tambahan informasi nama kasir -->
                <p class="mt-2" style="font-size: 14px;">
                    <strong>Nama Kasir:</strong> {{ $penjualan->kasir->nama_kasir ?? '-' }}
                </p>
                <p class="mt-2" style="font-size: 14px;">
                    <strong>Slot Kasir:</strong> {{ $penjualan->kasir->slot_kasir ?? '-' }}
                </p>                
                
            
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
            <div class="modal-body text-center">
                <div id="struk{{ $penjualan->id }}" class="struk-container">
                    <style>
                        .struk-container {
                            max-width: 300px;
                            margin: 0 auto;
                            font-family: 'Courier New', Courier, monospace;
                            font-size: 11px;
                            text-align: center;
                            padding: 10px;
                        }

                        .title {
                            font-size: 13px;
                            font-weight: bold;
                            margin-bottom: 2px;
                        }

                        .subtitle {
                            font-size: 10px;
                            margin-bottom: 8px;
                            line-height: 1.2;
                        }

                        .line {
                            border-top: 1px dashed #000;
                            margin: 5px 0;
                        }

                        table {
                            width: 100%;
                            font-size: 10px;
                            border-collapse: collapse;
                            text-align: left;
                        }

                        td {
                            vertical-align: top;
                            padding: 2px 0;
                        }

                        .right {
                            text-align: right;
                        }

                        .bold {
                            font-weight: bold;
                        }

                        .footer {
                            font-size: 10px;
                            margin-top: 6px;
                            line-height: 1.3;
                        }

                        @media print {
                            body * {
                                visibility: hidden;
                            }

                            .struk-container,
                            .struk-container * {
                                visibility: visible;
                            }

                            .struk-container {
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                margin: 0 auto;
                            }
                        }
                    </style>

                    <!-- STRUK CONTENT -->
                    <p class="title">Kasir Caffe</p>
                    <p class="subtitle">Jl. Merdeka Belajar No.12<br>Bandung - Jawa Barat</p>
                    <div class="line"></div>

                    <table>
                        <tr>
                            <td>No Faktur</td>
                            <td class="right bold">{{ $penjualan->id }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td class="right">{{ $penjualan->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Kasir</td>
                            <td class="right">{{ $penjualan->kasir->nama_kasir ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="line"></div>

                    <table>
                        @foreach($penjualan->detailTransaksi ?? [] as $detail)
                        <tr>
                            <td colspan="2">{{ $detail->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</td>
                        </tr>
                        <tr>
                            <td>{{ $detail->jumlah }} x Rp {{ number_format($detail->sub_total / max($detail->jumlah, 1), 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </table>

                    <div class="line"></div>

                    <table>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="right"><strong>Rp {{ number_format($penjualan->total_bayar ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Bayar</td>
                            <td class="right">Rp {{ number_format($penjualan->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Kembalian</td>
                            <td class="right">Rp {{ number_format(($penjualan->jumlah_bayar ?? 0) - ($penjualan->total_bayar ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="line"></div>

                    <p class="footer">Terima Kasih atas kunjungan Anda!<br>~ Kasir Caffe ~</p>
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
@endsection

@push('script')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#transaksiTable').DataTable({
        "language": {
            "search": "Cari Nama:", // Teks tetap di samping kiri input
            "searchPlaceholder": "Cari data transaksi...", // Placeholder dalam input
            "zeroRecords": "Tidak ada transaksi ditemukan"
            
        }
    });

    // filter berdasarkan tanggal transaksi

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
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    setTimeout(() => {
        if (type === 'all') {
            $('.transaksi-row').show();
        } else {
            $('.transaksi-row').hide();
            $('.transaksi-row[data-type="' + type + '"]').show();
        }

        Swal.close(); // Tutup SweetAlert setelah proses selesai
    }, 1500); // Simulasi loading selama 1 detik
}

</script>
<script>
    function printStruk(id) {
        const struk = document.getElementById(id).innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Struk Pembelian</title>
                <style>
                    body {
                        font-family: monospace;
                        font-size: 12px;
                        text-align: center;
                        padding: 20px;
                    }
                    .title {
                        font-size: 18px;
                        font-weight: bold;
                    }
                    .line {
                        border-top: 1px dashed #000;
                        margin: 8px 0;
                    }
                    .right {
                        text-align: right;
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="struk-container">
                    ${struk}
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
<script>
    $(document).ready(function () {
        $('#startDate, #endDate').on('change', function () {
            filterByDate();
        });
    });
    
    function filterByDate() {
        const start = $('#startDate').val();
        const end = $('#endDate').val();
    
        if (!start || !end) {
            $('#transaksiTable tbody tr').show();
            return;
        }
    
        const startDate = new Date(start);
        const endDate = new Date(end);
    
        $('#transaksiTable tbody tr').each(function () {
            const dateText = $(this).find('td:nth-child(6)').text(); 
            const rowDate = new Date(dateText.split(' ')[0].split('-').reverse().join('-')); 
    
            if (rowDate >= startDate && rowDate <= endDate) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    function resetDateFilter() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#transaksiTable tbody tr').show();
    }
    </script>    
@endpush

