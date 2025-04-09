@extends('admin.layouts.base')
@section('title', 'Transaksi Penjualan')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

@push('style')
    <style>
        .card {
        width: 12rem; /* Ukuran card lebih kecil */
        padding: 8px;
    }

    .produk-img {
        width: 100px; /* Ukuran gambar seragam */
        height: 100px; /* Pastikan ukuran sama */
        object-fit: cover; /* Potong gambar agar rapi */
        display: block;
        margin: 0 auto; /* Pusatkan gambar */
    }

    .card-body {
        text-align: center; /* Pusatkan teks */
        padding: 10px;
    }

    .card-title {
        font-size: 14px; /* Ukuran teks lebih kecil */
        font-weight: bold;
    }

    .card-text {
        font-size: 12px; /* Ukuran teks harga lebih kecil */
        margin-bottom: 8px;
    }

    .form-check-label {
        font-size: 12px;
    }
        .card-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #ddd;
    }
    .custom-toast {
        animation: slideInRight 0.5s ease-in-out; /* Animasi muncul dari kanan */
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        padding: 10px 15px;
        min-width: 280px; /* Ukuran lebih lebar agar tidak bertumpuk */
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100px); /* Muncul dari kanan */
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .divider {
        height: 2px;
        background-color: #ccc;
        width: 100%;
        margin: 20px 0;
        margin-top: 5px;
    }
    .form-check-input {
    appearance: none; /* Hilangkan gaya bawaan */
    width: 16px;
    height: 16px;
    border: 2px solid #34495e; /* Warna border agar lebih terlihat */
    border-radius: 50%; /* Agar tetap berbentuk lingkaran */
    outline: none; /* Hindari efek klik yang aneh */
    background-color: #fff; /* Warna dasar */
    position: relative;
    cursor: pointer;
}

/* Saat radio button dipilih */
.form-check-input:checked {
    background-color: #89AC46; /* Warna merah saat dipilih */
    border: 2px solid #89AC46;
}

/* Tambahkan lingkaran dalam agar terlihat jelas */
.form-check-input:checked::before {
    content: "";
    width: 8px;
    height: 8px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
#form_member,
#tipe_pelanggan {
    transition: all 0.3s ease;
}

.form-select,
.form-control {
    border-radius: 0.5rem;
    padding: 0.75rem;
    font-size: 0.95rem;
}

label.form-label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}
.produk-img {
        height: 120px;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
        transition: transform 0.3s ease;
    }

    .card:hover .produk-img {
        transform: scale(1.05);
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .card-text {
        font-size: 13px;
        color: #6c757d;
    }

    .produk-extra {
        font-size: 12px;
        color: #999;
    }

    .form-check-input:checked + .form-check-label::before {
        content: "✓ ";
        color: green;
        font-weight: bold;
    }
    .table thead th {
        background-color: #34495e;
        color: #fff;
        font-weight: 600;
        border-bottom: 2px solid #222221;
        padding: 12px;
    }
        .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #34495e;
        color: white;
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6;
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f2f2f2;
    }
    </style>
    @endpush
    <div class="container">
            <!-- Toast Container -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                @foreach(Auth::user()->unreadNotifications as $notification)
                    <div class="toast align-items-center text-white bg-success border-0 fade show custom-toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-bell me-2"></i> {{ $notification->data['message'] }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                @endforeach
            </div>            
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-shopping-cart"></i> Transaksi Penjualan
                    </h1>
                    <p class="text-muted">
                        <a href="{{ route('dashboard') }}" class="text-custom text-decoration-none">Home</a> / 
                        <a href="#" class="text-custom text-decoration-none">Transaksi Penjualan</a>
                    </p>                
                </div>
            </div>    
            <div class="divider"></div>

        <div class="d-flex flex-column flex-md-row gap-4 w-100">
            <div class="flex-grow-1">
                <label for="tipe_pelanggan" class="form-label fw-semibold">Pilih Tipe Pelanggan</label>
                <select id="tipe_pelanggan" class="form-select shadow-sm" onchange="togglePelangganForm()">
                    <option value="member">Pelanggan Member</option>
                    <option value="lain">Pelanggan Lain</option>
                </select>
            </div>
        
            <div class="flex-grow-1" id="form_member">
                <label for="pelanggan" class="form-label fw-semibold">Pelanggan Member</label>
                <select id="pelanggan" class="form-select shadow-sm">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Tambah Produk -->
        <div class="form-group">
            <label class="form-label" style="margin-top: 10px;">Pilih Produk:</label>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3"> <!-- 5 kolom di layar md+ -->
                @foreach($produk as $p)
                    <div class="col">
                        <div class="card h-100">
                            <img src="{{ asset('assets/produk_fotos/' . $p->foto) }}" class="card-img-top produk-img" alt="{{ $p->nama_produk }}">
                            <div class="card-body">
                                <h6 class="card-title">{{ $p->nama_produk }}</h6>
                                <p class="card-text">Rp{{ number_format($p->harga, 0, ',', '.') }}</p>
                                <div class="form-check d-flex justify-content-center mt-2">
                                    <input class="form-check-input me-2" type="radio" name="produk" id="produk{{ $p->id }}"
                                        value="{{ $p->id }}" data-harga="{{ $p->harga }}"
                                        data-foto="{{ asset('storage/produk_fotos/' . $p->foto) }}" onchange="updateFotoProduk()">
                                    <label class="form-check-label" for="produk{{ $p->id }}">
                                        {{ $p->nama_produk }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        
            <div class="input-group mt-3">
                <span class="input-group-text"><i class="bi bi-cart-plus"></i></span>
                <input type="number" id="jumlah" class="form-control" placeholder="Jumlah" min="1" value="1">
                <button onclick="tambahProduk()" class="btn" style="background-color: #34495e; color: white;">
                    <i class="bi bi-plus-lg"></i> Tambah Produk
                </button>
            </div>
        </div>
        
        <!-- Keranjang -->
        <div class="card-container" style="margin-bottom: 30px;">
            <h1 class="h3 mb-4 text-gray-800 ms-3">
                <i class="bi bi-cart-check"></i> Keranjang
            </h1>
            
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">Produk</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="keranjang"></tbody>
            </table>
        
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="total-box">
                    <strong>Total: Rp <span id="totalBayar">0</span></strong>
                </div>
                <button onclick="simpanTransaksi()" class="btn text-white" style="background-color: #89AC46;">
                    <i class="fas fa-shopping-cart"></i> Simpan Keranjang
                </button>                
            </div>            
        </div>

@endsection
@push('script')
    <script>
        let keranjang = [];
        function tambahProduk() {
            let produkElements = document.getElementsByName('produk');
            let produkId = null;

            produkElements.forEach(element => {
                if (element.checked) {
                    produkId = element.value;
                }
            });

            if (!produkId) {
                alert("Produk harus dipilih.");
                return;
            }

            let jumlah = parseInt(document.getElementById('jumlah').value);

            if (jumlah <= 0) {
                alert("Jumlah produk harus lebih dari 0.");
                return;
            }

            // Cek apakah produk sudah ada di keranjang
            let existingProduct = keranjang.find(item => item.id === produkId);

            if (existingProduct) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Menu sudah ada di keranjang!',
                    text: `Menu ${existingProduct.nama} sudah dimasukkan.`,
                });
                return; // Hentikan proses jika produk sudah ada
            }

            let harga = parseFloat(document.querySelector(`#produk${produkId}`).getAttribute('data-harga'));
            let nama = document.querySelector(`#produk${produkId}`).nextElementSibling.textContent;

            let item = {
                id: produkId,
                nama: nama,
                harga: harga,
                jumlah: jumlah,
                subtotal: harga * jumlah
            };

            keranjang.push(item);
            renderKeranjang();
        }

        // function tambahProduk() {
        //     let produkElements = document.getElementsByName('produk');
        //     let produkId;
        //     produkElements.forEach(element => {
        //         if (element.checked) {
        //             produkId = element.value;
        //         }
        //     });

        //     if (!produkId) {
        //         alert("Produk harus dipilih.");
        //         return;
        //     }

        //     let jumlah = document.getElementById('jumlah').value;

        //     if (jumlah <= 0) {
        //         alert("Jumlah produk harus lebih dari 0.");
        //         return;
        //     }

        //     let harga = document.querySelector(`#produk${produkId}`).getAttribute('data-harga');
        //     let nama = document.querySelector(`#produk${produkId}`).nextElementSibling.textContent;

        //     let item = {
        //         id: produkId,
        //         nama: nama,
        //         harga: parseFloat(harga),
        //         jumlah: parseInt(jumlah),
        //         subtotal: parseFloat(harga) * parseInt(jumlah)
        //     };

        //     keranjang.push(item);
        //     renderKeranjang();
        // }

        function renderKeranjang() {
            let tbody = document.getElementById('keranjang');
            tbody.innerHTML = "";
            let total = 0;

            keranjang.forEach((item, index) => {
                total += item.subtotal;
                tbody.innerHTML += `
        <tr>
            <td>${item.nama}</td>
            <td>Rp${item.harga.toLocaleString('id-ID')}</td>
            <td>${item.jumlah}</td>
            <td>Rp${item.subtotal.toLocaleString('id-ID')}</td>
            <td>
                <button onclick="hapusProduk(${index})" class="btn btn-danger"><i class="bi bi-trash3"></i></button>
            </td>
        </tr>
    `;
            });

            document.getElementById('totalBayar').innerText = total.toLocaleString('id-ID');
        }

        function hapusProduk(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        function simpanTransaksi() {
            let tipePelanggan = document.getElementById("tipe_pelanggan").value;
            let pelangganId = tipePelanggan === "member" ? document.getElementById("pelanggan").value : 0;

            if (keranjang.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang kosong!',
                    text: 'Silakan tambahkan produk terlebih dahulu.',
                });
                return;
            }

            let produkData = keranjang.map(item => ({
                produk_id: item.id,
                jumlah: item.jumlah
            }));

            let requestData = {
                pelanggan_id: parseInt(pelangganId), // Jika pelanggan lain, otomatis = 0
                produk: produkData,
            };

            fetch("{{ route('penjualan.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(requestData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            html: `
                               <div style="font-size: 14px; line-height: 1.6; max-width: 420px; margin: auto;">
                                <!-- No Faktur di tengah -->
                                <p style="text-align: center;"><strong>No Faktur:</strong> ${data.no_faktur}</p>

                                <!-- Data 2 kolom di tengah -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 10px auto; max-width: 360px;">
                                    <div style="text-align: center;">
                                        <p style="margin: 0;"><strong>Tanggal Transaksi</strong></p>
                                        <p style="margin: 0;">${new Date().toLocaleDateString()}</p>
                                    </div>
                                    <div style="text-align: center;">
                                        <p style="margin: 0;"><strong>Pembayaran</strong></p>
                                        <p style="margin: 0;">${data.metode_pembayaran || 'Tunai'}</p>
                                    </div>
                                    <div style="text-align: center;">
                                        <p style="margin: 0;"><strong>Total Bayar</strong></p>
                                        <p style="margin: 0;">Rp ${data.total_bayar.toLocaleString()}</p>
                                    </div>
                                    <div style="text-align: center;">
                                       <p style="margin: 0;"><strong>Kasir</strong></p>
                                      <p style="margin: 0;">${data.kasir || '-'}</p>
                                    </div>
                                </div>

                                <hr style="margin: 12px 0;">
                                <p style="font-style: italic; text-align: center;">Terima kasih atas kunjungan Anda!</p>
                            </div>

                            `,
                            width: '420px',
                            iconColor: '#28a745',
                            background: '#fff',
                            color: '#333',
                            confirmButtonText: '<i class="fa-solid fa-check"></i> OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(() => {
                            window.location.href =
                                `{{ route('admin.pembayaran.show', ['no_faktur' => '__NO_FAKTUR__']) }}`
                                .replace('__NO_FAKTUR__', data.no_faktur);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error,
                        });
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan coba lagi.',
                    });
                });
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let checkoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];
            let keranjangTable = document.getElementById("keranjang");
            let totalBayar = 0;

            checkoutData.forEach((item) => {
                let subtotal = item.harga * item.jumlah;
                totalBayar += subtotal;

                let row = `
            <tr>
                <td>${item.nama_produk}</td>
                <td>Rp${item.harga.toLocaleString()}</td>
                <td>${item.jumlah}</td>
                <td>Rp${subtotal.toLocaleString()}</td>
                <td><button class="btn btn-danger btn-sm" onclick="hapusItem('${item.id}')">Hapus</button></td>
            </tr>
        `;
                keranjangTable.innerHTML += row;
            });

            document.getElementById("totalBayar").innerText = totalBayar.toLocaleString();
        });
    </script>
    <script>
        function updateFotoProduk() {
            let produk = document.getElementById("produk");
            let foto = produk.options[produk.selectedIndex].getAttribute("data-foto");
            let imgElement = document.getElementById("fotoProduk");

            if (foto && foto !== "null" && foto !== "") {
                imgElement.src = foto;
                imgElement.style.display = "block";
            } else {
                imgElement.style.display = "none";
            }
        }
    </script>
    <script>
        function togglePelangganForm() {
            let tipePelanggan = document.getElementById("tipe_pelanggan").value;
            let formMember = document.getElementById("form_member");

            if (tipePelanggan === "member") {
                formMember.style.display = "block";
            } else {
                formMember.style.display = "none";
            }
        }
    </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 3000 }) // 3 detik
        });

        toastList.forEach((toast, index) => {
            setTimeout(() => {
                toast.show();
            }, index * 500); // Animasi bertahap setiap 500ms
        });
    });
</script>
<script>
    function togglePelangganForm() {
    const tipe = document.getElementById('tipe_pelanggan').value;
    const formMember = document.getElementById('form_member');
    if (tipe === 'member') {
        formMember.style.display = 'block';
    } else {
        formMember.style.display = 'none';
    }
}
window.addEventListener('DOMContentLoaded', () => {
    togglePelangganForm(); // Initial state
});
</script>
@endpush
