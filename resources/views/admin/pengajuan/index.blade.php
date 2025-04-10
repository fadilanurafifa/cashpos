@extends('admin.layouts.base')

@section('title', 'Manajemen Pengajuan Barang')

@section('content')
    @push('style')
        <style>
            .btn-custom {
                background-color: #89AC46;
                color: white;
                border: none;
                padding: 8px 14px;
                border-radius: 5px;
                font-size: 14px;
                cursor: pointer;
                white-space: nowrap;
            }

            .btn-custom:hover,
            .btn-custom:focus,
            .btn-custom:active {
                background-color: #89AC46 !important;
                color: white !important;
                box-shadow: none !important;
                outline: none !important;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 20px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 20px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 14px;
                width: 14px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: #4CAF50;
            }

            input:checked+.slider:before {
                transform: translateX(20px);
            }   
            .dataTables_filter {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 10px; /* Memberikan jarak antara teks dan input */
            }

            .dataTables_filter label {
                display: flex;
                align-items: center;
                gap: 10px;
                white-space: nowrap; /* Mencegah teks turun ke bawah */
            }
            .dataTables_length select,
            .dataTables_filter input {
                border-radius: 6px !important;
                border: 1px solid #ccc !important;
                padding: 5px 10px !important;
                outline: none !important;
            }


            .dataTables_paginate .paginate_button {
                background: transparent !important; /* Menghilangkan background hitam */
            }

             .dataTables_paginate .paginate_button.current {
                background: #007bff !important; 
                color: white !important;
                border: 1px solid #007bff !important;
            } 

            .dataTables_paginate .paginate_button:hover {
                background: #e9ecef !important; 
                border: 1px solid #007bff !important;
            } 
            /* Menghapus border bawah tabel */
            .dataTables_wrapper table.dataTable {
                border-bottom: none !important;
            }

            /* Menghapus garis bawah dari wrapper DataTables */
            .dataTables_wrapper {
                border-bottom: none !important;
                box-shadow: none !important;
            }
            .dataTables_wrapper .row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap; /* Agar responsif */
            }
           
            .modal-dialog.custom-size {
                width: 400px;
                max-width: 90%;
            }

            .modal-body {
                max-height: 420px; /* Dinaikkan dari 350px */
                overflow-y: auto;  /* Biar muncul scroll kalau terlalu panjang */
                padding-bottom: 10px; /* Tambahan padding bawah */
            }

            .form-group {
                margin-bottom: 10px;
            }

            .modal .form-control {
                font-size: 14px;
                padding: 4px 8px;
            }

            .modal .btn {
                padding: 6px 12px;
                font-size: 14px;
            }
            .form-group label {
                font-weight: bold;
                font-size: 13px;
                color: #333;
                margin-bottom: 4px;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    @endpush
    <div class="container">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        @endif
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-fw fa-folder"></i></i> Pengajuan Menu
                </h1>
                <p class="text-muted">
                    <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> /
                    <a href="#" class="text-custom text-decoration-none">Manajemen Pengajuan Menu</a>
                </p>
            </div>
            <div class="d-flex justify-content-end align-items-center gap-2">
                <button class="btn btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Pengajuan
                </button>
                
                <a href="{{ route('pengajuan.export.excel') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                
                <a href="{{ route('pengajuan.export.pdf') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>            
        </div>
                <div class="card table-container">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pengajuanTable" class="table table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Pengaju</th>
                                <th>Nama Barang</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Jumlah</th>
                                <th>Terpenuhi ?</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengajuan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->pelanggan->nama }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->tanggal_pengajuan }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>
                                        <form action="{{ route('admin.pengajuan.updateStatus', $item->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <label class="switch">
                                                <input type="checkbox" name="status" value="terpenuhi"
                                                    onchange="updateStatus(this, '{{ route('admin.pengajuan.updateStatus', $item->id) }}')"
                                                    {{ $item->status == 'terpenuhi' ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm editBtn" data-id="{{ $item->id }}"
                                            data-pelanggan_id="{{ $item->pelanggan_id }}"
                                            data-nama_barang="{{ $item->nama_barang }}"
                                            data-tanggal_pengajuan="{{ $item->tanggal_pengajuan }}"
                                            data-qty="{{ $item->qty }}" data-status="{{ $item->status }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form class="deleteForm" action="{{ route('admin.pengajuan.destroy', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm deleteBtn">
                                            <i class="fas fa-trash"></i> 
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog custom-size">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengajuan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.pengajuan.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="pelanggan_id">Nama Pengaju :</label>
                                <select name="pelanggan_id" class="form-control" required>
                                    <option value="">Pilih Nama Pengaju</option>
                                    @foreach ($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id }}">{{ $pelanggan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label for="nama_barang">Nama Menu :</label>
                                <input type="text" name="nama_barang" class="form-control" required placeholder="Masukkan Nama Menu">
                            </div>
        
                            <div class="form-group">
                                <label for="tanggal_pengajuan">Tanggal Pengajuan :</label>
                                <input type="date" name="tanggal_pengajuan" class="form-control" value="<?= date('Y-m-d') ?>" required readonly>
                            </div>
        
                            <div class="form-group">
                                <label for="qty">Jumlah :</label>
                                <input type="number" name="qty" class="form-control" required placeholder="Masukkan Jumlah">
                            </div>
        
                            <div class="form-group">
                                <label>Status :</label>
                                <select name="status" class="form-control" disabled>
                                    <option value="tidak terpenuhi" selected>Belum Terpenuhi</option>
                                </select>
                                <input type="hidden" name="status" value="tidak terpenuhi">
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog custom-size"> <!-- Ubah ukuran di sini -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Pengajuan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="editForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="edit_id" name="id">
        
                            <div class="form-group">
                                <label for="edit_pelanggan_id">Nama Pengaju :</label>
                                <select id="edit_pelanggan_id" name="pelanggan_id" class="form-control" required>
                                    <option value="">Pilih Nama Pengaju</option>
                                    @foreach ($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id }}">{{ $pelanggan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label for="edit_nama_barang">Nama Menu :</label>
                                <input type="text" id="edit_nama_barang" name="nama_barang" class="form-control" required placeholder="Masukkan Nama Menu">
                            </div>
        
                            <div class="form-group">
                                <label for="edit_tanggal_pengajuan">Tanggal Pengajuan :</label>
                                <input type="date" id="edit_tanggal_pengajuan" name="tanggal_pengajuan" class="form-control" required readonly>
                            </div>
        
                            <div class="form-group">
                                <label for="edit_qty">Jumlah :</label>
                                <input type="number" id="edit_qty" name="qty" class="form-control" required placeholder="Masukkan Jumlah">
                            </div>
                        </div>
        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    @endsection
        

    @push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let table = $('#pengajuanTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            // Menambahkan placeholder ke input pencarian bawaan DataTables
            $('.dataTables_filter input').attr('placeholder', 'Cari data pengajuan...');

            // Menata ulang tampilan Show dan Search agar sejajar
            $('.dataTables_wrapper .row').css({
                "display": "flex",
                "justify-content": "space-between",
                "align-items": "center",
                "flex-wrap": "wrap"
            });
        });
        </script>
        <script>
         document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll(".editBtn");
            const editForm = document.getElementById("editForm");

            editButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");
                    const pelanggan_id = this.getAttribute("data-pelanggan_id");
                    const nama_barang = this.getAttribute("data-nama_barang");
                    const tanggal_pengajuan = this.getAttribute("data-tanggal_pengajuan");
                    const qty = this.getAttribute("data-qty");

                    // Pastikan ID ditemukan sebelum mengatur form
                    if (id) {
                        document.getElementById("edit_id").value = id;
                        document.getElementById("edit_pelanggan_id").value = pelanggan_id;
                        document.getElementById("edit_nama_barang").value = nama_barang;
                        document.getElementById("edit_tanggal_pengajuan").value = tanggal_pengajuan;
                        document.getElementById("edit_qty").value = qty;

                        // Set action form agar mengarah ke URL yang benar
                        editForm.action = `/pengajuan/${id}`;
                    } else {
                        console.error("ID tidak ditemukan di tombol edit");
                    }
                });
            });
        });
        </script>
        <script>
            function updateStatus(element, url) {
                let status = element.checked ? "terpenuhi" : "tidak terpenuhi";

                fetch(url, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Gagal memperbarui status!");
                        }
                        return response.text(); // Ambil sebagai teks terlebih dahulu
                    })
                    .then(text => {
                        try {
                            return JSON.parse(text); // Coba ubah ke JSON
                        } catch {
                            return {
                                success: false,
                                message: "Respons tidak valid dari server"
                            }; // Default jika bukan JSON
                        }
                    })
                    .then(data => {
                        console.log("Response dari server:", data);
                        Swal.fire({
                            title: "Sukses!",
                            text: data.message || "Status berhasil diperbarui.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            title: "Error!",
                            text: "Gagal memperbarui status.",
                            icon: "error"
                        });
                    });
            }
        </script>
       <script>
            document.addEventListener("DOMContentLoaded", function() {
                const deleteButtons = document.querySelectorAll(".deleteBtn");
            
                deleteButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Apakah Anda yakin ingin menghapus data ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33", // Merah untuk hapus
                            cancelButtonColor: "#007bff", // Biru untuk batal
                            confirmButtonText: "Ya, Hapus!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.closest(".deleteForm").submit();
                            }
                        });
                    });
                });
            });
        </script>
@endpush