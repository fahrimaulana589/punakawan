@php
    use App\Models\Akun;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Saldo Bulan {{ $bulan }}</title>
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
  Saldo Bulan {{ $bulan }}

  <br>
  <br>

  <table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead>
      <tr>
        <th>No Akun</th>
        <th>Nama Akun</th>
        <th>Debet</th>
        <th>Kredit</th>
      </tr>
    </thead>
    <tbody>
      @php
        $totaldebet = 0;
        $totalkredit = 0;
      @endphp
      @foreach ($data as $akun)
        @php
          if ($akun['debet'] > 0) {
            $totaldebet += $akun['debet'];
          }
          if ($akun['kredit'] > 0) {
            $totalkredit += $akun['kredit'];
          }
        @endphp
        <tr>
          <td>
            {{ $akun['kode'] }}
          </td>
          <td>
            {{ $akun['nama'] }}
          </td>
            <td>
            {{ $akun['debet'] > 1 ? format_uang($akun['debet']) : '' }}
            </td>
            <td>
            {{ $akun['kredit'] > 1 ? format_uang($akun['kredit']) : '' }}
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
          <td>
            
          </td>
          <td>
            SALDO 
          </td>
          <td>
            {{ format_uang($totaldebet) }}
          </td>
          <td>
            {{ format_uang($totalkredit) }}
          </td>
          </tr>
        </tfoot>
        </table>
</body>
</html>