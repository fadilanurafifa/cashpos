@extends('admin.layouts.base')

@section('title', 'Manajemen Kategori')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
    .table-container {
        padding: 20px;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    .search-container {
        margin-bottom: 15px;
    }
    .modal-dialog {
        max-width: 400px; 
    }
    .modal-content {
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
    .modal-body {
        padding: 15px;
        background: #f8f9fa;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 6px 10px; 
        font-size: 13px; 
        height: 32px; 
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
.form-group label {
        font-weight: bold;
        font-size: 13px;
        color: #333;
        margin-bottom: 4px;
    }
</style>
@endpush
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-fw fa-tags"></i> Manajemen Kategori
            </h1>
            <p class="text-muted">
                <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="#" class="text-custom text-decoration-none">Manajemen Kategori</a>
            </p>                
        </div>
        
        <div class="d-flex justify-content-end align-items-center gap-2" style="margin-bottom: 15px;">
            <button class="btn btn-custom" data-toggle="modal" data-target="#tambahKategoriModal" style="border-radius: 5px;">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
            <a href="{{ route('kategori.exportExcel') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            
            <a href="{{ route('kategori.exportPDF') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>  
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('kategori.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
            @csrf
            <input type="file" name="file" class="form-control" required style="width: 340px;">
            <button class="btn" style="background-color: #4CAF50; color: white; font-size: 14px; padding: 5px 10px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <i class="fas fa-file-excel"></i>
            </button>                                 
        </form>
    </div>          
    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table id="kategoriTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kategori as $kat)
                        <tr class="text-center" id="row-{{ $kat->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kat->nama_kategori }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary editKategori" 
                                data-id="{{ $kat->id }}" 
                                data-nama="{{ $kat->nama_kategori }}" 
                                data-toggle="modal" data-target="#editKategoriModal">
                                <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger hapusKategori" data-id="{{ $kat->id }}">
                                    <i class="fas fa-trash"></i> 
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>    
</div> 

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori :</label>
                        <input type="text" name="nama_kategori" class="form-control" required placeholder="Masukkan Nama Kategori">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times-circle me-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>            
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editKategoriForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_kategori">Nama Kategori :</label>
                        <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>            
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
     @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000
        });
    @endif
</script>
<script>
$(document).ready(function() {
    let table = $('#kategoriTable').DataTable({
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
    $('.dataTables_filter input').attr('placeholder', 'Cari data kategori...');
    });

    $(document).on('click', '.hapusKategori', function(event) {
        event.preventDefault();
        let kategoriId = $(this).data('id');
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/kategori/' + kategoriId,
                    type: 'DELETE',
                    data: { _token: token },
                    success: function(response) {
                        Swal.fire('Dihapus!', 'Kategori berhasil dihapus.', 'success');
                        $("#row-" + kategoriId).fadeOut(500, function() {
                            $(this).remove();
                        });
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });
});
</script>
<script>
    $(document).ready(function() {
        // Ketika tombol edit diklik
        $(document).on('click', '.editKategori', function() {
            let kategoriId = $(this).data('id');
            let kategoriNama = $(this).data('nama');
    
            $('#edit_nama_kategori').val(kategoriNama); // Isi input modal dengan data kategori yang dipilih
            $('#editKategoriForm').attr('action', '/admin/kategori/' + kategoriId); // Set action form
        });
    
        // Submit Form Edit dengan AJAX
        $('#editKategoriForm').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let url = form.attr('action');
    
            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Swal.fire('Sukses!', 'Kategori berhasil diperbarui.', 'success')
                        .then(() => location.reload()); // Reload halaman setelah sukses
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                }
            });
        });
    });
</script>    
@endpush
