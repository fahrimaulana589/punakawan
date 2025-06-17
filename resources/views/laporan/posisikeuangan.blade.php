<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title> Laporan Bulan {{ $bulan }}</title>
    
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
  Posisi Keuangan Bulan {{ $bulan }}

  <br>
  <br>
  <table border="1" cellspacing="0" cellpadding="5">
  <thead>
    <tr>
      <th colspan="4">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="4">Neraca</th>
    </tr>
    <tr>
      <th colspan="4">PER {{ format_tanggal($hari) }}</th>
    </tr>
    <tr>
      <th colspan="2">AKTIVA</th>
      <th colspan="2">PASSIVA</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="2"><strong>AKTIVA LANCAR</strong></td>
      <td colspan="2"></td>
    </tr>
    <tr>
      <td>KAS</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_1']['debet']) }}</td>
      <td><strong>MODAL</strong></td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_6']['kredit']) }}</td>
    </tr>
    <tr>
      <td>PERLENGKAPAN</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_4']['debet']) }}</td>
      <td>IKHTISAR LABA RUGI</td>
      <td>{{ format_uang($laba_bersih) }}</td>
    </tr>
    <tr>
      <td>PERSEDIAAN BAHAN BAKU</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['ajp_1']['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>PERSEDIAAN BAHAN PENOLONG</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['ajp_5']['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>PERSEDIAAN PRODUK JADI</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['ajp_14']['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      @php
        $total_aktifa_lancar = 
        $split['Neraca Saldo Disesuikan']['ajp_5']['debet'] +
        $split['Neraca Saldo Disesuikan']["ajp_1"]['debet'] + 
        $split['Neraca Saldo Disesuikan']["saldo_4"]['debet'] +
        $split['Neraca Saldo Disesuikan']["saldo_1"]['debet'] +
        $split['Neraca Saldo Disesuikan']['ajp_14']['debet'];
        
      @endphp
      <td><strong>TOTAL AKTIVA LANCAR</strong></td>
      <td><strong>{{ format_uang($total_aktifa_lancar) }}</strong></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td colspan="2"><strong>AKTIVA TETAP</strong></td>
      <td colspan="2"></td>
    </tr>
    <tr>
      <td>PERALATAN</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']["saldo_5"]['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>AKUMULASI PENYUSUTAN PERALATAN</td>
      <td>{{ format_uang(isset($split['Neraca Saldo Disesuikan']["saldo_14"]['kredit']) ? $split['Neraca Saldo Disesuikan']["saldo_14"]['kredit'] : $split['Neraca Saldo Disesuikan']["ajp_12"]['kredit']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      @php
        $total_aktifa_tetap =$split['Neraca Saldo Disesuikan']["saldo_5"]['debet'] - (isset($split['Neraca Saldo Disesuikan']["saldo_14"]['kredit']) ? $split['Neraca Saldo Disesuikan']["saldo_14"]['kredit'] : $split['Neraca Saldo Disesuikan']["ajp_12"]['kredit']); 
      @endphp
      <td><strong>TOTAL AKTIVA TETAP</strong></td>
      <td><strong>{{ format_uang($total_aktifa_tetap) }}</strong></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      @php
        $total_aktifa = $total_aktifa_lancar + $total_aktifa_tetap;

      @endphp
      <td><strong>TOTAL AKTIVA</strong></td>
      <td><strong>{{ format_uang($total_aktifa) }}</strong></td>
      <td><strong>TOTAL PASSIVA</strong></td>
      <td><strong>{{ format_uang($modal_akhir) }}</strong></td>
    </tr>
  </tbody>
  </table>

</body>
</html>
