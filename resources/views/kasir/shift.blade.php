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
            border-radius: 10px;
        }

        .form-control,
        .form-select {
            background-color: #2d3748;
            border: 1px solid #4a5568;
            color: #f0f6fc;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .form-label {
            color: #e2e8f0;
        }

        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-danger {
            border-color: #ef4444;
            color: #ef4444;
        }

        .btn-outline-danger:hover {
            background-color: #ef4444;
            color: #fff;
        }

        a {
            color: #60a5fa;
        }

        a:hover {
            color: #3b82f6;
        }
        .mb-4 {
            color: white;
        }
        .text {
            font-size: 14px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card card-dark p-4 shadow" style="width: 100%; max-width: 480px;">
            <div class="text-center mb-4">
                <img src="https://img.icons8.com/ios-filled/50/ffffff/restaurant-menu.png" alt="logo" style="width: 50px;">
                <h4 class="mt-3">Mulai Shift Kasir</h4>
                <p class="text">Masukkan data sebelum memulai shift!</p>
            </div>

            <!-- Form Shift -->
            <form action="{{ route('shift.store') }}" method="POST">
                @csrf            

                <div class="mb-3">
                    <label for="slot" class="form-label">Pilih Slot Kasir</label>
                    <select name="slot" id="slot" class="form-select" required>
                        <option value="">-- Pilih Slot --</option>
                        <option value="Kasir 1">Kasir 1</option>
                        <option value="Kasir 2">Kasir 2</option>
                        <option value="Kasir 3">Kasir 3</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kasir</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Lengkap" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Mulai Shift</button>
            </form>

            <!-- Tombol Logout -->
            <div class="text-center">
                <a href="#" class="btn btn-outline-danger w-100"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
