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
  Jurnal Umum Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>

  <table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead>
      <tr>
        <th>TANGGAL</th>
        <th colspan="2">NAMA AKUN</th>
        <th>DEBET</th>
        <th>KREDIT</th>
      </tr>
    </thead>

    </tbody>
      @php
        $keseluruhan_debet = 0;
        $keseluruhan_kredit = 0;
      @endphp
      @foreach ($data as $item)
      @php
        $keseluruhan_debet += $item['total'];
        $keseluruhan_kredit += $item['kredit_total'];
      @endphp  
        <tr>
          <td>{{ format_tanggal($item['tanggal']) }}</td>
          <td colspan="1">{{ $item['nama'] }}</td>
          <td></td>
          <td>{{ format_uang($item['total']) }}</td>
          <td></td>
        </tr>
        @if ($item['kredit'] != null)
          
        <tr>
          <td></td>
          <td></td>
          <td colspan="1">{{ $item['kredit'] }}</td>
          <td></td>
          <td>{{ format_uang($item['kredit_total']) }}</td>
        </tr>
        @endif

      @endforeach
    </tbody>

    <tfoot>
      <tr>
          <td colspan="2" style="text-align: right;"><strong>Total</strong></td>
          <td></td>
          <td>{{ format_uang($keseluruhan_debet) }}</td>
          <td>{{ format_uang($keseluruhan_kredit)  }}</td>
      </tr>
  </tfoot>
  </table>
</body>
</html>