<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
        }

        .container {
            width: 58mm; 
            padding: 5px;
            text-align: center;
            border: 1px solid transparent; 
        }

        .title {
            font-size: 13px;
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        table {
            width: 100%;
            text-align: left;
            font-size: 10px;
        }

        td {
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
        @media print {
            @page {
                size: 58mm auto; 
                margin: 0; 
            }

            body {
                width: 58mm;
                height: auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                width: 58mm;
                height: auto;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">  
    <div class="container">
        <p class="title">Kasir Caffe</p>
        <p>Jl. Merdeka Belajar No.12<br>Bandung - Jawa Barat</p>
        <div class="line"></div>

        @if(isset($transaksi) && isset($detail_penjualan))
        <p class="bold">No Faktur: {{ $transaksi->no_faktur ?? '-' }}</p>
        <p class="bold">Tanggal: {{ isset($transaksi->created_at) ? date('d-m-Y H:i', strtotime($transaksi->created_at)) : '-' }}</p>
        <p class="bold">Kasir: {{ $transaksi->kasir->nama_kasir ?? '-' }}</p>
        <div class="line"></div>
        
            <table>
                @foreach ($detail_penjualan as $detail)
                    <tr>
                        <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                        <td class="right">{{ $detail->jumlah }} x Rp {{ number_format($detail->sub_total / max($detail->jumlah, 1), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>

            <div class="line"></div>
            <table>
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="right"><strong>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td>Bayar</td>
                    <td class="right">Rp {{ number_format($transaksi->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Kembalian</td>
                    <td class="right">Rp {{ number_format(max(($transaksi->jumlah_bayar ?? 0) - $transaksi->total_bayar, 0), 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="line"></div>
            <p>Terima Kasih atas kunjungan Anda!<br>~ Kasir Caffe ~</p>
        @else
            <p>Data tidak tersedia.</p>
        @endif
    </div>
</body>
</html>
