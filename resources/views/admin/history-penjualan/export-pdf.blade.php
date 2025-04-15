<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Penjualan</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 12px;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            padding: 15px;
        }

        .kop-surat img {
            width: 100px;
            height: auto;
            position: absolute;
            left: 20px;
            top: 5px;
        }

        .kop-text {
            display: inline-block;
            text-align: center;
            width: 100%;
        }

        .kop-surat h1 {
            margin: 5px 0;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }

        .kop-surat p {
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
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center; /* Header tetap di tengah */
        background-color: #aec3c7;
        color: #333;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        }

        td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left; /* Isi data tabel di kiri */
        }


        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }

            .kop-surat {
                margin-bottom: 10px;
            }

            /* Tambahkan untuk memastikan warna header table tetap muncul */
            td.header-cell {
                background: #aec3c7 !important;
                color: #333 !important;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .table-header-row td {
            text-align: center !important;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <img src="{{ asset('assets/img/logoitem.png') }}" alt="Logo">
        <div class="kop-text">
            <h1>Temu Rasa</h1>
            <p>Jl. Merdeka Belajar No. 10, Kota Bandung, Jawa Barat - Indonesia</p>
            <p>Email: info@TemuRasaPOS.com | Telp: (021) 123456</p>
        </div>
    </div>
    <div class="garis"></div>

    <h2>Laporan Transaksi Penjualan</h2>
    <p style="text-align: center; font-weight: bold;">
        Periode: {{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}
    </p>

    <table>
        <!-- Kosongkan <thead> untuk mencegah pengulangan otomatis saat print -->
        <thead></thead>
    
        <tbody>
            <!-- HEADER: ditaruh di baris pertama tbody agar hanya muncul sekali -->
            <tr class="table-header-row" style="background-color: #aec3c7; color: #333; font-size: 14px; font-weight: bold; text-transform: uppercase;">
                <td class="header-cell">ID</td>
                <td class="header-cell">Pelanggan</td>
                <td class="header-cell">Kasir</td>
                <td class="header-cell">Total</td>
                <td class="header-cell">Status</td>
                <td class="header-cell">Tanggal</td>
            </tr>
    
            <!-- DATA -->
            @foreach($penjualan as $trx)
            <tr>
                <td>{{ $trx->id }}</td>
                <td>{{ $trx->pelanggan->nama ?? 'Biasa' }}</td>
                <td>{{ $trx->kasir->nama_kasir ?? '-' }}</td>
                <td>Rp{{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                <td>{{ ucfirst($trx->status_pembayaran) }}</td>
                <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    
</body>
</html>
