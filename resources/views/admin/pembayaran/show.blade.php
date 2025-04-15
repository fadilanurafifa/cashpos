@extends('admin.layouts.base')

@section('title', 'Pembayaran - ' . $transaksi->no_faktur)

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@push('style')
    <style>
      .btn-cetak-struk {
            background-color: #007bff !important;
            color: white !important;
            border: none !important;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 15px;
            transition: none;
        }

        .btn-cetak-struk:hover,
        .btn-cetak-struk:focus,
        .btn-cetak-struk:active {
            background-color: #007bff !important;
            color: white !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-cetak-struk:active {
            transform: scale(0.98);
        }
        .uang-cepat {
            margin-right: 5px;
            margin-top: 5px;
        }

        .alert-kembalian {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .alert-kembalian {
            height: 60px !important; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #jumlah_bayar {
            height: 100% !important; 
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .btn-success-custom {
            background: linear-gradient(135deg, #28d17c, #1ea85c); 
            color: #ffffff;
            border: none;
            padding: 0.9rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 6px 14px rgba(30, 168, 92, 0.3);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-success-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(30, 168, 92, 0.35);
        }

        .btn-success-custom i {
            color: linear-gradient(135deg, #28d17c, #1ea85c);
        }

        .btn-success-custom:disabled {
            background: #c3e6cb !important;
            color: #2f4f2f !important;
            box-shadow: none !important;
            cursor: not-allowed;
            opacity: 1;
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }
        .btn[style*="#89AC46"]:hover {
            background-color: #769636 !important;
        }
    </style>
@endpush
@if (session('jumlah_bayar') && session('kembalian'))
<script>
    Swal.fire({
        icon: 'success',
        title: '<span style="font-size:18px;">Detail Transaksi</span>',
        html: `
            <div style="font-size: 15px; line-height: 1.6;">
                <b>Jumlah Bayar:</b> Rp {{ number_format(session('jumlah_bayar')) }} <br>
                <b>Jumlah Kembalian:</b> Rp {{ number_format(session('kembalian')) }}
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Berhasil!',
        confirmButtonColor: '#27ae60', 
        iconColor: '#2ecc71',
        background: '#ffffff',
        color: '#2d3436',
        backdrop: 'rgba(0, 0, 0, 0.3)',
        width: '360px',
        padding: '1.4rem',
    });
</script>
@endif
<div class="container mt-4" style="margin-bottom: 30px;">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-receipt"></i> Pembayaran untuk Faktur <strong>#{{ $transaksi->no_faktur }}</strong>
    </h1>

    @if($transaksi->status_pembayaran == 'pending')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> Status Pembayaran: <strong>Pending</strong>
        </div>
    @else
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Pembayaran telah <strong>berhasil</strong>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label><strong>Nama Kasir:</strong></label>
                    <input type="text" class="form-control" value="{{ $transaksi->kasir->nama_kasir ?? '-' }}" readonly>
                </div>
            
                <div class="col-md-4">
                    <label><strong>Pelanggan:</strong></label>
                    <input type="text" class="form-control" value="{{ $transaksi->pelanggan->nama ?? 'Pelanggan Biasa' }}" readonly>
                </div>
            
                <div class="col-md-4">
                    <label><strong>Total Bayar:</strong></label>
                    <input type="text" class="form-control" value="Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}" readonly>
                </div>
            </div>
            

            <div class="table-responsive">
                <table id="detailTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-box-open me-1 text-muted"></i> Nama Produk</th>
                            <th><i class="fas fa-sort-numeric-up me-1 text-muted"></i> Jumlah</th>
                            <th><i class="fas fa-money-bill-wave me-1 text-muted"></i> Harga Satuan</th>
                            <th><i class="fas fa-coins me-1 text-muted"></i> Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail_penjualan as $detail)
                            @php
                                $harga_satuan = $detail->sub_total / $detail->jumlah;
                            @endphp
                            <tr> 
                                <td>{{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>Rp {{ number_format($harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            

            @if($transaksi->status_pembayaran == 'pending')
                <hr>
                <h4><i class="fas fa-cash-register"></i> Form Pembayaran</h4>
                <form id="pembayaranForm" action="{{ route('admin.pembayaran.bayar', $transaksi->no_faktur) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="row g-3">
                        <!-- Form Jumlah Bayar -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-5">Jumlah Bayar:</label>
                            <div class="alert alert-success alert-kembalian p-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                                Rp.
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" 
                                    class="form-control text-center border-0 bg-transparent fw-bold fs-4 w-100 h-100"
                                    required min="{{ $transaksi->total_bayar }}">
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm uang-cepat" data-value="50000">+ Rp 50.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm uang-cepat" data-value="100000">+ Rp 100.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm uang-cepat" data-value="200000">+ Rp 200.000</button>
                                <button type="button" class="btn btn-danger btn-sm ms-2" id="clearJumlahBayar">
                                    <i class="fas fa-times"></i> Hapus
                                </button>
                            </div>
                        </div>
                        
                        <!-- Form Kembalian -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-5">Kembalian:</label>
                            <div class="alert alert-success alert-kembalian p-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                                <span id="kembalianView" class="fw-bold fs-4"></span>
                            </div>
                        </div>
                    </div>
                    
                    
                    <button type="submit" class="btn w-100 mt-4 btn-lg shadow-sm border-0" 
                    style="background-color: #89AC46; color: white; font-weight: 600; letter-spacing: 0.5px;">
                    <i class="fas fa-credit-card me-2"></i>Lakukan Pembayaran!
                </button>
                
                </form>
            @else
                <button class="btn btn-success-custom w-100 mt-3 d-flex align-items-center justify-content-center gap-2" disabled>
                  <i class="fas fa-check-double fa-lg"></i>
                    <span>Transaksi Sukses!</span>
                </button>
                <a href="{{ route('admin.pembayaran.print', $transaksi->no_faktur) }}" class="btn btn-cetak-struk" target="_blank">
                    <i class="fas fa-print"></i> Cetak Struk
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi datatable
        $('#detailTable').DataTable({
            responsive: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
        });

        // Variabel input
        const bayarInput = document.getElementById("jumlah_bayar");
        const totalBayar = parseInt("{{ $transaksi->total_bayar }}");
        const kembalianView = document.getElementById("kembalianView");
        const kembalianBox = document.getElementById("kembalianBox");

        // Hitung kembalian ketika jumlah bayar diinput
        bayarInput.addEventListener("input", function () {
            let bayar = parseInt(bayarInput.value) || 0;
            let kembalian = bayar - totalBayar;

            // Validasi jika kurang dari total bayar
            if (bayar < totalBayar) {
                bayarInput.setCustomValidity("Jumlah bayar tidak boleh kurang dari total");
            } else {
                bayarInput.setCustomValidity("");
            }

            // Tampilkan kembalian jika cukup
            if (kembalian >= 0) {
                kembalianView.textContent = kembalian.toLocaleString('id-ID');
                kembalianBox.style.display = "block";
            } else {
                kembalianBox.style.display = "none";
            }
        });

        // Tombol tambah uang cepat
        $(".uang-cepat").on("click", function () {
            let tambah = parseInt($(this).data("value"));
            let current = parseInt(bayarInput.value) || 0;
            bayarInput.value = current + tambah;
            bayarInput.dispatchEvent(new Event("input"));
        });

        // Tombol Hapus nominal
        $("#clearJumlahBayar").on("click", function () {
            bayarInput.value = "";
            bayarInput.dispatchEvent(new Event("input"));
            bayarInput.focus();
        });

        // Konfirmasi pembayaran
        $("#pembayaranForm").submit(function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: 'Apakah Anda yakin ingin melakukan pembayaran?',
                icon: 'warning',
                iconColor: '#f39c12', // Warna ikon tetap eye-catching
                showCancelButton: true,
                buttonsStyling: false, // Matikan gaya default SweetAlert
                confirmButtonText: '<i class="fa-solid fa-check"></i> Ya, bayar sekarang!',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Batal',
                customClass: {
                    confirmButton: 'btn btn-success me-2', // Tombol hijau Bootstrap
                    cancelButton: 'btn btn-danger' // Tombol merah Bootstrap
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
    const bayarInput = document.getElementById("jumlah_bayar");
    const totalBayar = parseInt("{{ $transaksi->total_bayar }}");
    const kembalianView = document.getElementById("kembalianView");

    bayarInput.addEventListener("input", function () {
        let bayar = parseInt(bayarInput.value) || 0; // Jika kosong, dianggap 0
        let kembalian = bayar - totalBayar;

        // Update tampilan kembalian
        kembalianView.textContent = "Rp " + (kembalian >= 0 ? kembalian.toLocaleString('id-ID') : "0");
    });

    // Tombol Hapus (Reset Kembalian ke Rp 0)
    document.getElementById("clearJumlahBayar").addEventListener("click", function () {
        bayarInput.value = "";
        kembalianView.textContent = "Rp 0";
    });
});
</script>
@endpush
