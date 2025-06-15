<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Slip Laporan Gaji</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      width: 210mm;
      margin: auto;
      background: #fff;
      box-sizing: border-box;
    }

    .header {
      position: relative;
      margin-bottom: 20px;
    }

    .company-info {
      position: absolute;
      left: 0;
      top: 0;
      width: 40%;
    }

    .salary-title {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    .employee-meta {
      position: absolute;
      right: 0;
      top: 0;
      width: 40%;
      text-align: right;
      font-size: 14px;
    }

    .info-table {
      width: 100%;
      margin-top: 60px;
      border-collapse: collapse;
    }

    .info-table td {
      vertical-align: top;
    }

    .gaji-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .gaji-table th, .gaji-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: right;
    }

    .gaji-table td:nth-child(2), .gaji-table th:nth-child(2) {
      text-align: left;
    }

    .bold {
      font-weight: bold;
    }

    .footer {
      margin-top: 60px;
      display: flex;
      justify-content: space-between;
    }

    .text-right {
      text-align: right;
    }

    .mt-20 {
      margin-top: 20px;
    }

    .total-row {
      margin-top: 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      font-size: 18px;
      font-weight: bold;
    }

    .total-label {
      margin-right: 10px;
    }
  </style>
</head>
<body>

  <div class="header">
    <div class="company-info">
      <strong>{{ $profile->nama }}</strong><br>
      {{ $profile->alamat }}<br>
      TELP. {{ $profile->handphone }}
    </div>
    <div class="salary-title">SLIP GAJI</div>
    <div class="employee-meta">
      Tanggal: {{ format_tanggal($slipgaji['tanggal_akhir']) }}<br>
      Kode Karyawan: {{ $slipgaji['kode'] }}
    </div>
  </div>

  <table class="info-table">
    <tr>
      <td width="20%">Nama</td>
      <td>: {{ $slipgaji['nama'] }}</td>
      <td width="20%">Alamat</td>
      <td>: {{ $slipgaji['alamat'] }}</td>
    </tr>
    <tr>
      <td>Jabatan</td>
      <td>: {{ $slipgaji['jabatan'] }}</td>
      <td>Telepon</td>
      <td>: {{ $slipgaji['no_hp'] }}</td>
    </tr>
  </table>

  <table class="gaji-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Keterangan</th>
        <th>Jumlah</th>
      </tr>
    </thead>
    <tbody>
      @php
        $index = 1;
        $total = $slipgaji['gaji_pokok'] * $slipgaji['hari_kerja'];
      @endphp  
      <tr>
        <td>{{ $index }}</td><td>Laporan Gaji Perhari {{ format_uang($slipgaji['gaji_pokok']) }} X {{ $slipgaji['hari_kerja'] }} Hari</td>
        <td>{{ format_uang($slipgaji['gaji_pokok'] * $slipgaji['hari_kerja']) }}</td>
      </tr>
      @foreach ($slipgaji['lainya'] as $lainya)
      @php
        $index++;
        $total += $lainya['lainya_pokok'] * $slipgaji['hari_kerja']; 
      @endphp
      <tr>
        <td>{{ $index }}</td><td>{{ $lainya['nama'] }} {{ format_uang($lainya['lainya_pokok']) }} X {{ $slipgaji['hari_kerja'] }} Hari</td>
        <td>{{ $lainya['total'] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="display: flex; align-items: center; justify-content: space-between;" class="mt-20">
    <em>{{ terbilang($total) }}</em>
    <div class="total-row" style="margin-top: 0;">
      <span class="total-label">TOTAL DITERIMA:</span>
      <span>{{ format_uang($total) }}</span>
    </div>
  </div>

  <div class="footer">
    <div>
      Penerima,<br><br><br><br><br>
      <strong>{{ $slipgaji['nama'] }}</strong>
    </div>
    <div class="text-right">
      {{ format_tanggal($slipgaji['tanggal_akhir']) }}<br><br><br><br><br>
      <strong>{{ $profile->nama }}</strong>
    </div>
  </div>

</body>
</html>
