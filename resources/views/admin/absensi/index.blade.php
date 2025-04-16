@extends('admin.layouts.base')

@section('title', 'Manajemen Absensi')

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
                <i class="fas fa-fw fa-check-circle"></i>
                Manajemen Absensi
            </h1>
            <p class="text-muted">
                <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="#" class="text-custom text-decoration-none">Manajemen Absensi</a>
            </p>                
        </div>
        
        <div class="d-flex justify-content-end align-items-center gap-2" style="margin-bottom: 15px;">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Absensi</a>
            </div>
        </div>
    </div>  
    <div class="d-flex justify-content-end mb-3">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a href="{{ route('absensi.export.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('absensi.export.pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a> 
            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="fas fa-upload"></i> Import Excel
            </a>
                                 
        </div>        
    </div>          
    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table id="absensiTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal Masuk</th>
                            <th>Waktu Masuk</th>
                            <th>Status</th>
                            <th>Waktu Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $index => $a)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($a->user->role == 'kasir' && $a->user->kasir)
                                    {{ $a->user->kasir->nama_kasir }}
                                @else
                                    {{ $a->user->name }}
                                @endif
                            </td>
                            
                            
                            <td>{{ $a->tanggal_masuk }}</td>
                            <td>{{ \Carbon\Carbon::parse($a->waktu_masuk)->format('H.i.s') }}</td>
                            <td>
                                <form method="POST" action="{{ route('absensi.updateStatus', $a->id) }}" class="status-form" id="statusForm-{{ $a->id }}">
                                    @csrf
                                    @method('PUT')
                                    <!-- Tambahkan ini agar controller tahu ini hanya update status -->
                                    <input type="hidden" name="status_only" value="true">
                            
                                    <div class="form-group">
                                        <select name="status_masuk" class="form-select form-select-sm status-dropdown" onchange="updateStatus({{ $a->id }})">
                                            <option value="masuk" {{ $a->status_masuk == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                            <option value="izin" {{ $a->status_masuk == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="alfa" {{ $a->status_masuk == 'alfa' ? 'selected' : '' }}>Alfa</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                                         
                            <td>
                                @if ($a->waktu_akhir_kerja)
                                    {{ \Carbon\Carbon::parse($a->waktu_akhir_kerja)->format('H:i:s') }}
                                @elseif ($a->status_masuk === 'masuk')
                                    <form method="POST" action="{{ route('absensi.selesai', $a->id) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Selesai Kerja</button>
                                    </form>
                                @else
                                    00:00:00
                                @endif
                            </td>
                            
                            <td class="d-flex gap-1">
                                <button class="btn btn-primary btn-sm btn-edit" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEdit"
                                data-id="{{ $a->id }}"
                                data-status="{{ $a->status_masuk }}">
                            <i class="fas fa-edit"></i>
                            </button>
                                <form method="POST" action="{{ route('absensi.destroy', $a->id) }}" class="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
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
</div> 

<!-- Modal Tambah Absensi -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('absensi.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Absensi</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_masuk" class="form-control" required>
                            <option value="masuk">Masuk</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa</option>
                        </select>                        
                    </div>

                    <div class="mb-3">
                        <label>Waktu Masuk</label>
                        <input type="datetime-local" name="waktu_masuk" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editForm" action="">
                @csrf
                @method('PUT') <!-- Menggunakan PUT method -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Input User ID -->
                  <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    
                    <!-- Input Tanggal Masuk -->
                    <div class="form-group mb-3">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                    </div>

                    <!-- Input Status Masuk -->
                    <div class="form-group mb-3">
                        <label for="status_masuk">Status</label>
                        <select name="status_masuk" id="status_masuk" class="form-select form-select-sm" required>
                            <option value="masuk">Masuk</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa</option>
                        </select>
                    </div>

                    <!-- Input Waktu Masuk -->
                    <div class="form-group mb-3">
                        <label for="waktu_masuk">Waktu Masuk</label>
                        <input type="time" class="form-control" id="waktu_masuk" name="waktu_masuk" required>
                    </div>

                    <!-- Input Waktu Akhir Kerja -->
                    <div class="form-group mb-3">
                        <label for="waktu_akhir_kerja">Waktu Akhir Kerja</label>
                        <input type="time" class="form-control" id="waktu_akhir_kerja" name="waktu_akhir_kerja" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editForm" action="">
                @csrf
                @method('PUT') <!-- Menggunakan PUT method -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Status Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="status_masuk">Status</label>
                        <select name="status_masuk" id="status_masuk" class="form-select form-select-sm" required>
                            <option value="masuk">Masuk</option>
                            <option value="izin">Izin</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

{{-- modal import --}}
<!-- Modal -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalImportLabel">Import Data Absensi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="file_import" class="form-label">Pilih File Excel</label>
              <input type="file" class="form-control" name="file_import" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Import</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
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
    let table = $('#absensiTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });

    $(document).ready(function() {

    // Menambahkan placeholder ke input pencarian bawaan DataTables
    $('.dataTables_filter input').attr('placeholder', 'Cari data Absensi...');
    });
});
</script>
<script>
    function updateStatus(id) {
    var form = document.getElementById('statusForm-' + id);
    form.submit();  // Submit the form automatically
}
</script>
<script>
    $(document).on('click', '.deleteForm button', function(event) {
        event.preventDefault(); // Mencegah form submit langsung
        
        // Konfirmasi menggunakan SweetAlert2
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna memilih "Ya, Hapus!", maka form akan disubmit
                $(this).closest('form').submit();  // Submit form untuk hapus data
            }
        });
    });
</script>
<script>
    // Script untuk mengisi data di modal
$('#modalEdit').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Tombol yang memicu modal
    var id = button.data('id');  // Mengambil data-id
    var status = button.data('status');  // Mengambil data-status

    var modal = $(this);
    modal.find('#status_masuk').val(status);  // Mengisi select dengan status yang dipilih
    modal.find('form').attr('action', '/absensi/' + id); // Mengubah action form menjadi rute update dengan id yang benar
});
    </script>
    <script>
        function updateStatus(id) {
    document.getElementById('statusForm-' + id).submit();
}

    </script>
@endpush