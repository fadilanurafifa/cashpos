@extends('admin.layouts.base')

@section('title', 'Slot Kasir')

@section('content')
<div class="container mt-4">
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif
<div>
    <h1 class="h3 text-gray-800">
        <i class="fas fa-fw fa-user"></i> Manajemen Slot Kasir
    </h1>    
    <p class="text-muted">
        <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
        <a href="#" class="text-custom text-decoration-none">Manajemen Slot Kasir</a>
    </p>                
</div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="kasirTable" class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Kasir</th>
                            <th>Slot Kasir</th>
                            {{-- <th>Dibuat</th>
                            <th>Diupdate</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasirs as $index => $kasir)
                        <tr class="text-center">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kasir->nama_kasir }}</td>
                            <td>{{ $kasir->slot_kasir }}</td>
                            {{-- <td>{{ $kasir->created_at }}</td>
                            <td>{{ $kasir->updated_at }}</td> --}}
                            <td>
                                <!-- Button Edit -->
                                <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $kasir->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Form Hapus -->
                                <form action="{{ route('slot_kasir.delete', $kasir->id) }}" method="POST" class="d-inline form-hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"> 
                                        <i class="fas fa-trash"></i></button>
                                </form>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal{{ $kasir->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('slot_kasir.update', $kasir->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">Edit Slot Kasir</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="nama_kasir">Nama Kasir</label>
                                                        <input type="text" name="nama_kasir" class="form-control" value="{{ $kasir->nama_kasir }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="slot_kasir">Slot Kasir</label>
                                                        <input type="text" name="slot_kasir" class="form-control" value="{{ $kasir->slot_kasir }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Simpan</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- End Modal -->
                            </td>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        let table = $('#kasirTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });
    $(document).ready(function() {
     // Menambahkan placeholder ke input pencarian bawaan DataTables
     $('.dataTables_filter input').attr('placeholder', 'Cari data Kasir...');
    });
    });
</script>
<script>
    document.querySelectorAll('.form-hapus').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

