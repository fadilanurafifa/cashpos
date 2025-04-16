<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 12px;
            background-color: #fff;
            padding: 20px;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .kop-surat img {
            width: 90px;
            height: auto;
            margin-right: 20px;
        }

        .kop-text {
            text-align: center;
            flex-grow: 1;
        }

        .kop-text h1 {
            margin: 5px 0;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 12px;
            color: #555;
        }

        .garis {
            border-bottom: 3px solid black;
            margin-top: 10px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 18px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #aec3c7;
            color: #333;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        @media print {
            th {
                background: #aec3c7 !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
                setTimeout(() => window.close(), 1000);
            }, 500);
        };
    </script>
</head>
<body>

    <div class="kop-surat">
        <img src="{{ asset('assets/img/logoitem.png') }}" alt="Logo">
        <div class="kop-text">
            <h1>Temu Rasa</h1>
            <p>Jl. Merdeka Belajar No. 10, Kota Bandung, Jawa Barat - Indonesia</p>
            <p>Email: info@TemuRasaPOS.com | Telp: (021) 123456</p>
        </div>
    </div>
    <div class="garis"></div>

    <h2>Laporan Data Absensi</h2>
    <p style="text-align: center; font-weight: bold;">
        Tanggal: {{ now()->format('d-m-Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Waktu Masuk</th>
                <th>Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $a->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($a->tanggal_masuk)->format('d-m-Y') }}</td>
                <td>{{ ucfirst($a->status_masuk) }}</td>
                <td>{{ $a->waktu_masuk }}</td>
                <td>{{ $a->waktu_akhir_kerja ?? '00:00:00' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
