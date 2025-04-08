<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulai Shift Kasir</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #0d1117;
            color: #f0f6fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-dark {
            background-color: #1f2937;
            border: none;
            border-radius: 12px;
            padding: 24px;
        }

        .form-control,
        .form-select {
            background-color: #2d3748;
            border: 1px solid #4a5568;
            color: #f0f6fc;
            font-size: 14px;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .form-label {
            color: #e2e8f0;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-danger {
            background-color: #ef4444;   
            color: #fff;                 
            border: none;               
        }

        .btn-outline-danger:hover {
            background-color: #ef4444;
            color: #fff;
            border: none;
        }

        a {
            color: #60a5fa;
        }

        a:hover {
            color: #3b82f6;
        }

        .text-muted-sm {
            font-size: 13px;
            font-style: italic;
            color: #94a3b8;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        .logo-img {
            width: 40px;
        }

        .form-section {
            margin-bottom: 18px;
        }

        @media (max-width: 576px) {
            .card-dark {
                padding: 16px;
            }
        }
        .form-label {
            color: rgb(194, 192, 192);
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card card-dark shadow" style="width: 100%; max-width: 360px;">
            <div class="text-center mb-3">
                <img src="https://img.icons8.com/ios-filled/50/ffffff/restaurant-menu.png" alt="logo" class="logo-img">
                <h5 class="card-title mt-2 text-white">Mulai Shift Kasir</h5>
                <p class="text-muted-sm">Masukkan data sebelum memulai shift!</p>
            </div>

            <!-- Form Shift -->
            <form action="{{ route('shift.store') }}" method="POST">
                @csrf

                <div class="form-section">
                    <label for="slot" class="form-label">Pilih Slot Kasir :</label>
                    <select name="slot" class="form-select" required>
                        <option value="">-- Pilih Slot --</option>
                        <option value="Kasir 1">Kasir 1</option>
                        <option value="Kasir 2">Kasir 2</option>
                        <option value="Kasir 3">Kasir 3</option>
                    </select>
                </div>

                <div class="form-section">
                    <label for="kasir_id" class="form-label">Nama Kasir :</label>
                    <select name="kasir_id" id="kasir_id" class="form-select" required>
                        <option value="">-- Pilih Kasir --</option>
                        <option value="lainnya">+ Tambah Nama Baru</option>
                        @foreach($daftarKasir as $kasir)
                            <option value="{{ $kasir->id }}">{{ $kasir->nama_kasir }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="nama_baru" id="nama_baru" class="form-control mt-2"
                           placeholder="Masukkan Nama Baru" style="display: none;">
                </div>

                <div class="form-section">
                    <button type="submit" class="btn btn-primary w-100">Mulai Shift</button>
                </div>
            </form>

            <!-- Tombol Cancel -->
            <div>
                <a href="#" class="btn btn-outline-danger w-100"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Cancel
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle input nama baru -->
    <script>
        document.getElementById('kasir_id').addEventListener('change', function () {
            const namaBaruInput = document.getElementById('nama_baru');
            namaBaruInput.style.display = this.value === 'lainnya' ? 'block' : 'none';
        });
    </script>
</body>
</html>
