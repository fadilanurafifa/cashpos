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
      background: #fff;
    }

    .container {
      width: 58mm;
      padding: 5px 10px;
      border: 1px solid transparent;
      text-align: center;
    }

    .title {
      font-size: 13px;
      font-weight: bold;
      margin-bottom: 2px;
    }

    .subtitle {
      font-size: 10px;
      margin-bottom: 8px;
      line-height: 1.2;
    }

    .line {
      border-top: 1px dashed #000;
      margin: 5px 0;
    }

    table {
      width: 100%;
      font-size: 10px;
      border-collapse: collapse;
    }

    td {
      vertical-align: top;
      padding: 2px 0;
    }

    .right {
      text-align: right;
    }

    .bold {
      font-weight: bold;
    }

    .total-table td {
      padding: 3px 0;
    }

    .footer {
      font-size: 10px;
      margin-top: 6px;
      line-height: 1.3;
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
        align-items: flex-start;
      }

      .container {
        width: 58mm;
        padding: 5px 10px;
      }
    }
  </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">
  <div class="container">
    <p class="title">Kasir Caffe</p>
    <p class="subtitle">Jl. Merdeka Belajar No.12<br>Bandung - Jawa Barat</p>
    <div class="line"></div>

    @if(isset($transaksi) && isset($detail_penjualan))
      <table>
        <tr>
          <td>No Faktur</td>
          <td class="right bold">{{ $transaksi->no_faktur ?? '-' }}</td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td class="right">{{ isset($transaksi->created_at) ? date('d-m-Y H:i', strtotime($transaksi->created_at)) : '-' }}</td>
        </tr>
        <tr>
          <td>Kasir</td>
          <td class="right">{{ $transaksi->kasir->nama_kasir ?? '-' }}</td>
        </tr>
      </table>

      <div class="line"></div>
      <table>
        @foreach ($detail_penjualan as $detail)
          <tr>
            <td colspan="2">{{ $detail->produk->nama_produk ?? '-' }}</td>
          </tr>
          <tr>
            <td>{{ $detail->jumlah }} x Rp {{ number_format($detail->sub_total / max($detail->jumlah, 1), 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </table>

      <div class="line"></div>
      <table class="total-table">
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
      <p class="footer">Terima Kasih atas kunjungan Anda!<br>~ Kasir Caffe ~</p>
    @else
      <p>Data tidak tersedia.</p>
    @endif
  </div>
</body>
</html>
