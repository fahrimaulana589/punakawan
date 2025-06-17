<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title> Laba Rugi Bulan {{ $bulan }}</title>
    
  <style>
    .page-break {
        page-break-before: always; /* atau page-break-after: always */
    }
    table {
      border-collapse: collapse;
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
  @php
    $harga_pokok_produksi = $split['Penyesuaian']['ajp_15']['kredit'];
    $produk_awal = $split['Penyesuaian']['saldo_15']['kredit'];
    $produk_akhir = $split['Penyesuaian']['ajp_14']['debet'];

    $produksi = $harga_pokok_produksi + $produk_awal - $produk_akhir; 
  @endphp
  Laba Rugi Bulan {{ $bulan }}

  <br>
  <br>
  <table style="width: 100%">
    <tr>
      <th colspan="3">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="3">LAPORAN LABA RUGI</th>
    </tr>
    <tr>
      <th colspan="3">PER {{ format_tanggal($hari) }}</th>
    </tr>

    <tr>
      <td>PENJUALAN</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_7']['kredit']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td >Harga Pokok Penjualan</td>
      <td >{{ format_uang($produksi) }}</td>
      <td ></td>
    </tr>
    <tr>
      <td>LABA KOTOR</td>
      <td></td>
      @php
        $laba_kotor = $split['Neraca Saldo Disesuikan']['saldo_7']['kredit'] - $produksi; 
      @endphp
      <td>{{ format_uang($laba_kotor) }}</td>
    </tr>
    @php
      $total_beban = $split['Neraca Saldo Disesuikan']['ajp_9']['debet'];
    @endphp
    @foreach ($bebans as $beban)
    @php
      $total_beban += $beban['total'];
    @endphp
    <tr>
      <td>{{ $beban['nama'] }}</td>
      <td>{{ format_uang($beban['total']) }}</td>
      <td></td> 
    </tr>
      
    @endforeach
    <tr>
      <td>BIAYA PERLENGKAPAN</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['ajp_9']['debet']) }}</td>
      <td></td>
    </tr>

    <tr>
      <td>TOTAL BIAYA</td>
      <td></td>
      <td>{{ format_uang($total_beban) }}</td>
    </tr>
    <tr>
      @php
        $laba_bersih = $laba_kotor - $total_beban;
      @endphp
      <td>LABA BERSIH</td>
      <td></td>
      <td>{{ format_uang($laba_bersih) }}</td>
    </tr>
  </table>
</body>
</html>
