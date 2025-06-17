<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title> Perubahan Modal Bulan {{ $bulan }}</title>
    
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
    $laba_kotor = $split['Neraca Saldo Disesuikan']['saldo_7']['kredit'] - $produksi;
    $total_beban = $split['Neraca Saldo Disesuikan']['ajp_9']['debet'];
  @endphp
  @foreach ($bebans as $beban)
    @php
      $total_beban += $beban['total'];
    @endphp    
  @endforeach
  @php
    $laba_bersih = $laba_kotor - $total_beban;
  @endphp
  @php
    $modal_akhir = $split['Neraca Saldo Disesuikan']['saldo_6']['kredit'] + $laba_bersih;
  @endphp
  
  Perubahan Modal Bulan {{ $bulan }}

  <br>
  <br>
  <table table style="width: 100%">
    <tr>
      <th colspan="2">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="2">PERUBAHAN MODAL</th>
    </tr>
    <tr>
      <th colspan="2">PER {{ format_tanggal($hari) }}</th>
    </tr>
    <tr>
      <td>MODAL AWAL</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_6']['kredit']) }}</td>
    </tr>
    <tr>
      <td>LABA BERSIH</td>
      <td>{{ format_uang($laba_bersih) }}</td>
    </tr>
    <tr>
      @php
        $modal_akhir = $split['Neraca Saldo Disesuikan']['saldo_6']['kredit'] + $laba_bersih;
      @endphp
      <td>MODAL AKHIR</td>
      <td>{{ format_uang($modal_akhir) }}</td>
    </tr>
  </table>

</body>
</html>
