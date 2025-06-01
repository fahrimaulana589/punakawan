<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title> Neraca Lajur Bulan {{ $bulan }}</title>
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
  Neraca Lajur Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>

  <table border="1" cellspacing="0" cellpadding="6">
  <thead>
    <tr>
      <th ></th>
      <th ></th>
      @foreach ($split as $key => $saldo)
      <th colspan="2">{{ $key }}</th>
      @endforeach
    </tr>
    <tr>
      <th>NO AKUN</th>
      <th>NAMA AKUN</th>
      @foreach ($split as $key => $saldo)
      <th>DEBET (Rp)</th>
      <th>KREDIT (Rp)</th>
      @endforeach
    </tr>
  </thead>  
  <tbody>
    @foreach ($data as $key => $item)
      <tr> 
        <td>{{ $key }}</td>
        {{-- <td>{{ $item['saldo']['kode'] }}</td> --}}
        <td>{{ $item['saldo']['nama'] }}</td>
        <td>{{ $item['saldo']['debet'] != 0 ? format_uang($item['saldo']['debet']) : '' }}</td>
        <td>{{ $item['saldo']['kredit'] != 0 ? format_uang($item['saldo']['kredit']) : '' }}</td>
        <td>{{ $item['penyesuian']['debet'] != 0 ? format_uang($item['penyesuian']['debet']) : '' }}</td>
        <td>{{ $item['penyesuian']['kredit'] != 0 ? format_uang($item['penyesuian']['kredit']) : '' }}</td>
        <td>{{ $item['saldo_penyesuaian']['debet'] != 0 ? format_uang($item['saldo_penyesuaian']['debet']) : '' }}</td>
        <td>{{ $item['saldo_penyesuaian']['kredit'] != 0 ? format_uang($item['saldo_penyesuaian']['kredit']) : '' }}</td>
        <td>{{ $item['laba rugi']['debet'] != 0 ? format_uang($item['laba rugi']['debet']) : '' }}</td>
        <td>{{ $item['laba rugi']['kredit'] != 0 ? format_uang($item['laba rugi']['kredit']) : '' }}</td>
        <td>{{ $item['neraca']['debet'] != 0 ? format_uang($item['neraca']['debet']) : '' }}</td>
        <td>{{ $item['neraca']['kredit'] != 0 ? format_uang($item['neraca']['kredit']) : '' }}</td>
      </tr>
    @endforeach
    <tr>
      <td></td>
      <td></td>
    @foreach ($split as $key => $saldo)
      @php
      $debet = collect($saldo)->sum('debet');
      $kredit = collect($saldo)->sum('kredit');  
      @endphp
      <td>{{ $debet != 0 ? format_uang($debet) : '' }}</td>
      <td>{{ $kredit != 0 ? format_uang($kredit) : '' }}</td>
    @endforeach
    </tr>
    <tr>
      <td></td>
      <td></td>
    @foreach ($split as $key => $saldo)
      @php
        $debet = collect($saldo)->sum('debet');
        $kredit = collect($saldo)->sum('kredit');  
      @endphp
      @if ($debet == $kredit)
      <td></td>
      <td></td>
      @elseif ($debet > $kredit)
      <td></td>
      <td>{{ ($debet - $kredit) != 0 ? format_uang($debet - $kredit) : '' }}</td>
      @elseif ($debet < $kredit)
      <td>{{ ($kredit - $debet) != 0 ? format_uang($kredit - $debet) : '' }}</td>
      <td></td>
      @endif
    @endforeach
    </tr>
  </tbody>
</table>
</body>
</html>