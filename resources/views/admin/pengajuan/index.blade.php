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
            /* Menghilangkan border pada input pencarian saat diklik */
            .dataTables_filter input {
                outline: none !important;
                box-shadow: none !important;
                background-color: #f8f9fa;
                padding: 5px 10px;
                border-radius: 5px;
                
            }

            /* Menghapus garis bawah di kolom terakhir */
            #PengajuanTable thead th:last-child,
            #PengajuanTable tbody td:last-child {
                border-right: none;
            }

            /* Menghapus garis bawah tabel */
            #PengajuanTable tbody tr:last-child td {
                border-bottom: none;
            }
            /* Menghilangkan garis bawah tabel */
            #PengajuanTable {
                border-bottom: none !important;
            }

            /* Menghilangkan garis bawah dari elemen wrapper */
            .dataTables_wrapper {
                border-bottom: none !important;
            }

            /* Menghilangkan garis pada baris terakhir */
            #PengajuanTable tbody tr:last-child td {
                border-bottom: none !important;
            }
            /* Styling input search agar sesuai dengan gambar */
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

            /* Styling label "Cari:" */
            .dataTables_filter label {
                font-weight: bold;
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
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Pengajuan
                </button>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('pengajuan.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('pengajuan.export.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

        </div>
        <div class="card table-container">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="PengajuanTable" class="table table-bordered display">
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
                                        <button class="btn btn-warning btn-sm editBtn" data-id="{{ $item->id }}"
                                            data-pelanggan_id="{{ $item->pelanggan_id }}"
                                            data-nama_barang="{{ $item->nama_barang }}"
                                            data-tanggal_pengajuan="{{ $item->tanggal_pengajuan }}"
                                            data-qty="{{ $item->qty }}" data-status="{{ $item->status }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                            Edit
                                        </button>
                                        <form class="deleteForm" action="{{ route('admin.pengajuan.destroy', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm deleteBtn">Hapus</button>
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
        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.pengajuan.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <select name="pelanggan_id" class="form-control" required>
                                <option value="">Pilih Nama Pengaju</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}">{{ $pelanggan->nama }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="nama_barang" class="form-control mt-2" placeholder="Nama Barang"
                                required>
                            <input type="date" name="tanggal_pengajuan" class="form-control mt-2"
                                value="<?= date('Y-m-d') ?>" required readonly>
                            <input type="number" name="qty" class="form-control mt-2" placeholder="Qty" required>
                            <select name="status" class="form-control mt-2" disabled>
                                <option value="tidak terpenuhi" selected>Belum Terpenuhi</option>
                            </select>
                            <input type="hidden" name="status" value="tidak terpenuhi">

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id" name="id">

                            <label for="edit_pelanggan_id">Nama Pengaju:</label>
                            <select id="edit_pelanggan_id" name="pelanggan_id" class="form-control" required>
                                <option value="">Pilih Nama Pengaju</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}">{{ $pelanggan->nama }}</option>
                                @endforeach
                            </select>

                            <label for="edit_nama_barang">Nama Barang:</label>
                            <input type="text" id="edit_nama_barang" name="nama_barang" class="form-control mt-2"
                                required>

                            <label for="edit_tanggal_pengajuan">Tanggal Pengajuan:</label>
                            <input type="date" id="edit_tanggal_pengajuan" name="tanggal_pengajuan"
                                class="form-control mt-2" required>

                            <label for="edit_qty">Quantity:</label>
                            <input type="number" id="edit_qty" name="qty" class="form-control mt-2" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" defer></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

        <script>
            $(document).ready(function() {
                let table = $('#PengajuanTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "lengthChange": false, // Menghilangkan dropdown "Show entries"
                    "language": {
                        "search": "Cari:", // Mengubah label search menjadi "Cari:"
                        "searchPlaceholder": "Cari data pengajuan..." // Menambahkan placeholder ke input search
                    }
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const editButtons = document.querySelectorAll(".editBtn");
                const editForm = document.getElementById("editForm");

                editButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        const id = this.dataset.id;
                        document.getElementById("edit_id").value = id;
                        document.getElementById("edit_pelanggan_id").value = this.dataset.pelanggan_id;
                        document.getElementById("edit_nama_barang").value = this.dataset.nama_barang;
                        document.getElementById("edit_tanggal_pengajuan").value = this.dataset
                            .tanggal_pengajuan;
                        document.getElementById("edit_qty").value = this.dataset.qty;
                        document.getElementById("edit_status").value = this.dataset.status;

                        // Pastikan form action diperbarui dengan ID yang benar
                        editForm.action = `/pengajuan/${id}`;
                    });
                });
            });
        </script>
        <script>
            function updateStatus(checkbox, url) {
                let status = checkbox.checked ? 'terpenuhi' : 'tidak terpenuhi';

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status: status,
                            _method: 'PUT'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Status updated:', data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const deleteForms = document.querySelectorAll("form[action*='pengajuan.destroy']");

                deleteForms.forEach(form => {
                    form.addEventListener("submit", function(event) {
                        event.preventDefault(); // Mencegah submit default

                        Swal.fire({
                            title: "Apakah Anda yakin?",
                            text: "Data yang dihapus tidak bisa dikembalikan!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Ya, hapus!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Jika dikonfirmasi, kirim form
                            }
                        });
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
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#6c757d",
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
    @endsection
