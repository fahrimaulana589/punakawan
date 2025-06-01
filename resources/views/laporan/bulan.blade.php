<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title> Laporan Bulan {{ $bulan }}</title>
    
  <style>
    table {
      border-collapse: collapse;
      width: 1000px;
      font-family: Arial, sans-serif;
    }
    td, th {
      border: 1px solid black;
      padding: 6px 10px;
      vertical-align: top;
    }
    .center {
      text-align: center;
    }
    .right {
      text-align: right;
    }
    .bold {
      font-weight: bold;
    }
  </style>
</head>
<body>
  Laporan Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>

  <table>
    <tr>
      <td colspan="5" class="center bold">PT PUNOKAWAN MANUNGGAL SEJAHTERA</td>
    </tr>
    <tr>
      <td colspan="5" class="center bold">LAPORAN HARGA POKOK PRODUKSI</td>
    </tr>
    <tr>
      <td colspan="5" class="center bold">PER 31 Maret 2025</td>
    </tr>
    
    <tr>
      <td class="bold">BAHAN BAKU</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    
    <tr>
      <td></td>
      <td>SALDO AWAL BAHAN BAKU</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan'][5]['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>PEMBELIAN BAHAN BAKU</td>
      <td>{{ format_uang($split['Penyesuaian'][5]['kredit']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>PERSEDIAAN BAHAN BAKU PRODUKSI</td>
      <td></td>
      @php
        $persedian_bb = $split['Neraca Saldo Disesuikan'][5]['debet'] + $split['Penyesuaian'][5]['kredit'];
      @endphp
      <td>{{ format_uang($persedian_bb) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>SALDO AKHIR BAHAN BAKU</td>
      <td></td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan'][13]['debet']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td class="bold">TOTAL BIAYA BAHAN BAKU</td>
      <td></td>
      <td></td>
      @php
        $total_bb = $persedian_bb - $split['Neraca Saldo Disesuikan'][13]['debet'];
      @endphp
      <td>
        {{ format_uang($total_bb) }}
      </td>
    </tr>

    <tr>
      <td class="bold">TENAGA KERJA LANGSUNG</td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan'][9]['debet']) }}</td>
    </tr>

    <tr>
      <td class="bold">OVERHEAD PABRIK</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>BIAYA BAHAN PENOLONG</td>
      <td></td>
      <td>{{ format_uang($split['Penyesuaian'][17]['debet']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>BIAYA PENYUSUTAN PERALATAN</td>
      <td></td>
      <td>{{ format_uang($split['Penyesuaian'][12]['kredit']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td><b>TOTAL BOP</b></td>
      <td></td>
      <td><b></b></td>
      @php
        $bop = $split['Penyesuaian'][12]['kredit'] + $split['Penyesuaian'][17]['debet'];
      @endphp
      <td><b>{{ format_uang($bop) }}</b></td>
    </tr>
    <tr>
      <td></td>
      <td><b>TOTAL BIAYA PRODUKSI</b></td>
      <td></td>
      <td><b></b></td>
       @php
        $produksi = $bop + $split['Neraca Saldo Disesuikan'][9]['debet'] + $total_bb;
      @endphp
      <td><b>{{ format_uang($produksi) }}</b></td>
    </tr>
  </table>
</body>
</html>
