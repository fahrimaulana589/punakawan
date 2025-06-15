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
  @endphp
  Laporan Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>
  <br>
  <table>
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
  <div class="page-break"></div>
  Laporan Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>
  <br>
  <table>
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
  <div class="page-break"></div>
  Laporan Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>
  <br>
  <table border="1" cellspacing="0" cellpadding="5">
  <thead>
    <tr>
      <th colspan="4">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="4">POSISI KEUANGAN</th>
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
