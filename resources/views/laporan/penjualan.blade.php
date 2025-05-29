<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Bulan {{ $bulan }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
  <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="4" style="text-align: center;"><strong>PENERIMAAN KAS</strong></th>
        </tr>
        <tr>
            <th style="text-align: left;">TANGGAL</th>
            <th style="text-align: left;">KETERANGAN</th>
            <th style="text-align: right;">KAS(D)</th>
            <th style="text-align: right;">PENJUALAN(K)</th>
        </tr>
    </thead>
    @php
      $totalkeseluruhan = 0;
    @endphp
    <tbody>
        @foreach ($data as $index => $total)
        @php
          $totalkeseluruhan += $total;
        @endphp
        <tr>
          <td>{{ format_tanggal($index) }}</td>
          <td>PENJUALAN</td>
          <td style="text-align: right;">{{ format_uang($total) }}</td>
          <td style="text-align: right;">{{ format_uang($total) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: right;"><strong>Total</strong></td>
            <td style="text-align: right;"><strong>{{ format_uang($totalkeseluruhan) }}</strong></td>
            <td style="text-align: right;"><strong>{{ format_uang($totalkeseluruhan) }}</strong></td>
        </tr>
    </tfoot>
  </table>

    
</body>
</html>