<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggan</title>
    <style>
        /* Styling Umum */
        body {
            font-family: "Arial", sans-serif;
            font-size: 12px;
            background-color: #fff;
            padding: 20px;
        }
        
        /* Kop Surat */
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

        /* Styling Tabel */
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
        }

        th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left; /* Menjadikan teks rata kiri */
        }

        th {
            background-color: #aec3c7;
            color: #333;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Baris Bergantian */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Hover Efek */
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
            <h1>Cash Caffe POS</h1>
            <p>Jl. Merdeka Belajar No. 10, Kota Bandung, Jawa Barat - Indonesia</p>
            <p>Email: info@CashPOS.com | Telp: (021) 123456</p>
        </div>
    </div>
    <div class="garis"></div>

    <h2>Laporan Data Pelanggan</h2>
    <p style="text-align: center; font-weight: bold;">
        Tanggal: {{ now()->format('d-m-Y H:i') }}
    </p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pelanggan</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No Telepon</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pelanggan as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->kode_pelanggan }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->alamat }}</td>
                <td>{{ $p->no_telp }}</td>
                <td>{{ $p->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
