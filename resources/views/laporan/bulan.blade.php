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
  Laporan Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>
  <br>
  
  <table>
    <tr>
      <th colspan="5">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="5">LAPORAN HARGA POKOK PRODUKSI</th>
    </tr>
    <tr>
      <th colspan="5">PER {{ format_tanggal($hari) }}</th>
    </tr>
    
    <tr>
      <td>BAHAN BAKU</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    
    <tr>
      <td></td>
      <td>SALDO AWAL BAHAN BAKU</td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_2']['debet']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>PEMBELIAN BAHAN BAKU</td>
      <td>{{ format_uang($split['Penyesuaian']['saldo_2']['kredit']) }}</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>PERSEDIAAN BAHAN BAKU PRODUKSI</td>
      <td></td>
      @php
        $persedian_bb = $split['Neraca Saldo Disesuikan']['saldo_2']['debet'] + $split['Penyesuaian']['saldo_2']['kredit'];
      @endphp
      <td>{{ format_uang($persedian_bb) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>SALDO AKHIR BAHAN BAKU</td>
      <td></td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['ajp_1']['debet']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>TOTAL BIAYA BAHAN BAKU</td>
      <td></td>
      <td></td>
      @php
        $total_bb = $persedian_bb - $split['Neraca Saldo Disesuikan']['ajp_1']['debet'];
      @endphp
      <td>
        {{ format_uang($total_bb) }}
      </td>
    </tr>

    <tr>
      <td>TENAGA KERJA LANGSUNG</td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{ format_uang($split['Neraca Saldo Disesuikan']['saldo_8']['debet']) }}</td>
    </tr>

    <tr>
      <td>OVERHEAD PABRIK</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>BIAYA BAHAN PENOLONG</td>
      <td></td>
      <td>{{ format_uang($split['Penyesuaian']['ajp_6']['debet']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>BIAYA PENYUSUTAN PERALATAN</td>
      <td></td>
      <td>{{ format_uang(isset($split['Penyesuaian']['saldo_14']['kredit']) ? $split['Penyesuaian']['saldo_14']['kredit'] : $split['Penyesuaian']['ajp_12']['kredit']) }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>TOTAL BOP</td>
      <td></td>
      <td></td>
      @php
        $bop = (isset($split['Penyesuaian']['saldo_14']['kredit']) ? $split['Penyesuaian']['saldo_14']['kredit'] : $split['Penyesuaian']['ajp_12']['kredit']) + $split['Penyesuaian']['ajp_6']['debet'];
      @endphp
      <td>{{ format_uang($bop) }}</td>
    </tr>
    <tr>
      <td></td>
      <td>TOTAL BIAYA PRODUKSI</td>
      <td></td>
      <td></td>
       @php
        $produksi = $bop + $split['Neraca Saldo Disesuikan']['saldo_8']['debet'] + $total_bb;
      @endphp
      <td>{{ format_uang($produksi) }}</td>
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
      <td >HPP</td>
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
      <th colspan="2">LAPORAN LABA RUGI</th>
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
      <th colspan="4">NERACA</th>
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
      @php
        $total_aktifa_lancar = 
        $split['Neraca Saldo Disesuikan']['ajp_5']['debet'] +
        $split['Neraca Saldo Disesuikan']["ajp_1"]['debet'] + 
        $split['Neraca Saldo Disesuikan']["saldo_4"]['debet'] +
        $split['Neraca Saldo Disesuikan']["saldo_1"]['debet'];
        
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
