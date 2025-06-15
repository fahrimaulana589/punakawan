<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title> Hpp Bulan {{ $bulan }}</title>
    
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
  Hpp Bulan {{ $bulan }}

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
      <th colspan="2">PT PUNOKAWAN MANUNGGAL SEJAHTERA</th>
    </tr>
    <tr>
      <th colspan="2">LAPORAN LABA RUGI</th>
    </tr>
    <tr>
      <th colspan="2">PER {{ format_tanggal($hari) }}</th>
    </tr>

    <tr>
      <td >Harga Pokok Produksi</td>
      <td >{{ format_uang($produksi) }}</td>
    </tr>
    
    <tr>
      <td>Persedian Awal Produk Jadi</td>
      @php
      // dd($split);
        $presedianAwalProduk = $split['Neraca Saldo']['saldo_15']['debet']; 
      @endphp
      <td>{{ format_uang($presedianAwalProduk) }}</td>
    </tr>

    <tr>
      <td>Persedian Akhir Produk Jadi</td>
      @php
        $presedianAkhirProduk = $split['Penyesuaian']['ajp_14']['debet']; 
      @endphp
      <td>{{ format_uang($presedianAkhirProduk) }}</td>
    </tr>
    
    @php
      $hargaPokokPenjual = $produksi + $presedianAwalProduk - $presedianAkhirProduk;
    @endphp
    
    <tr>
      <td>Harga Pokok Penjualan</td>
      <td>{{ format_uang($hargaPokokPenjual) }}</td>
    </tr>
  </table>

</body>
</html>
