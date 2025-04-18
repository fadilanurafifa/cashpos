@extends('admin.layouts.base')

@section('title', 'Daftar Pesanan')

@section('content')
@push('style')
<style>
    .table {
        background: #ffffff;
        border-radius: 8px;
    }
    .table th {
        background: #f8f9fa;
    }
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Garis merah di sebelah kiri untuk pesanan prioritas */
    .priority-row {
        border-left: 6px solid #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.05) !important;
    }

    /* Badge untuk menandai pesanan prioritas */
    .priority-badge {
        background-color: #dc3545;
        color: white;
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        margin-left: 20px;
    }
</style>
@endpush

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-clipboard-list"></i> Daftar Pesanan
            </h1>
            <p class="text-muted">
                <a href="{{ route('chef.dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                <a href="{{ route('chef.index') }}" class="text-custom text-decoration-none">Daftar Pesanan</a>
            </p>                
        </div>
    </div>            

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                {{-- <th>No</th> --}}
                <th>No Faktur</th>
                <th>Produk</th>
                <th>Status Pembayaran</th>
                <th>Status Pesanan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders->sortBy('created_at') as $index => $order)
            <tr class="{{ $index < 3 ? 'priority-row' : '' }}">
                {{-- <td>{{ $index + 1 }}</td> --}}
                <td>
                    {{ $order->no_faktur }}
                    @if($index < 3)
                        <span class="priority-badge"><i class="fas fa-fire priority-icon"></i></span>
                    @endif
                </td>
                <td>
                    @foreach ($order->detail_penjualan as $detail)
                        <p>{{ $detail->produk->nama_produk }}</p>
                    @endforeach
                </td>
                <td>
                    <span class="fw-bold text-{{ $order->status_pembayaran == 'lunas' ? 'success' : 'danger' }}">
                        {{ ucfirst($order->status_pembayaran) }}
                    </span>                    
                </td>
                <td>
                    <span class="fw-bold text-{{ 
                        $order->status_pesanan == 'pending' ? 'warning' : 
                        ($order->status_pesanan == 'proses memasak' ? 'danger' : 'success') }}">
                        {{ ucfirst($order->status_pesanan) }}
                    </span>
                </td>
                <td>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#detailModal{{ $order->id }}" 
                        class="d-flex justify-content-center align-items-center">
                         <i class="fas fa-eye text-dark fa-lg"></i> 
                     </a>                                                          
                </td>
            </tr>

            <!-- Modal Detail Pesanan -->
            <div class="modal fade" id="detailModal{{ $order->id }}" tabindex="-1" 
                aria-labelledby="modalLabel{{ $order->id }}" aria-hidden="true">
               <div class="modal-dialog modal-md"> 
                   <div class="modal-content shadow rounded-3">
                       <!-- Modal Header -->
                       <div class="modal-header bg-light border-bottom">
                           <h6 class="modal-title fw-bold">Detail Pesanan → {{ $order->no_faktur }}</h6>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
           
                       <!-- Modal Body -->
                       <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;">
                           <div class="container">
                               <!-- Kode Pesanan -->
                               <div class="row mb-2">
                                   <div class="col-5 text-muted small">Kode Pesanan</div>
                                   <div class="col-auto small">:</div>
                                   <div class="col-6 small fw-bold">{{ $order->no_faktur }}</div>
                               </div>
           
                               <!-- Status Pembayaran -->
                               <div class="row mb-2">
                                   <div class="col-5 text-muted small">Status Pembayaran</div>
                                   <div class="col-auto small">:</div>
                                   <div class="col-6 small fw-bold text-{{ $order->status_pembayaran == 'lunas' ? 'success' : 'danger' }}">
                                       {{ ucfirst($order->status_pembayaran) }}
                                   </div>
                               </div>
           
                               <!-- Nama Kasir -->
                                <div class="row mb-2">
                                    <div class="col-5 text-muted small">Nama Kasir</div>
                                    <div class="col-auto small">:</div>
                                    <div class="col-6 small fw-bold">
                                        {{ $order->kasir->nama_kasir }}  <!-- Assuming the relationship is defined correctly -->
                                    </div>
                                </div>
                               <!-- Status Pesanan -->
                               <div class="row mb-3">
                                   <div class="col-5 text-muted small">Status Pesanan</div>
                                   <div class="col-auto small">:</div>
                                   <div class="col-6 small fw-bold text-{{ 
                                       $order->status_pesanan == 'pending' ? 'warning' : 
                                       ($order->status_pesanan == 'proses memasak' ? 'primary' : 'success') }}">
                                       {{ ucfirst($order->status_pesanan) }}
                                   </div>
                               </div>
           
                               <!-- Produk -->
                               <h6 class="border-bottom pb-1 mb-2 small">Produk :</h6>
                               <ul class="list-group list-group-flush small">
                                   @foreach($order->detail_penjualan as $detail)
                                       <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                           <span>{{ $detail->produk->nama_produk }}</span>
                                           <span class="fw-bold text-danger">x{{ $detail->jumlah }}</span>
                                       </li>
                                   @endforeach
                               </ul>                                
                           </div>
                       </div>
           
                       <!-- Modal Footer -->
                       <div class="modal-footer bg-light border-top p-2 d-flex justify-content-end gap-2">
                           @if($order->status_pesanan == 'pending')
                               <form action="{{ route('chef.updateOrder', $order->id) }}" method="POST" class="d-inline">
                                   @csrf
                                   @method('PUT')
                                   <input type="hidden" name="status_pesanan" value="proses memasak">
                                   <button type="submit" class="btn btn-sm btn-primary">
                                       <i class="fas fa-fire"></i>  Masak
                                   </button>                                    
                               </form>
                           @elseif($order->status_pesanan == 'proses memasak')
                               <form action="{{ route('chef.updateOrder', $order->id) }}" method="POST" class="d-inline">
                                   @csrf
                                   @method('PUT')
                                   <input type="hidden" name="status_pesanan" value="selesai">
                                   <button type="submit" class="btn btn-sm btn-success">
                                       <i class="fas fa-check"></i> Selesai
                                   </button>
                               </form>
                           @endif
                       </div>                        
                   </div>
               </div>
           </div>
           
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 3000 })
        });
        toastList.forEach(toast => toast.show());
    });
</script>
@endpush
