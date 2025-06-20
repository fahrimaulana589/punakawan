<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title> Neraca Lajur Bulan {{ $bulan }}</title>
    <style>
      html{
        width: 420mm;
        margin: 0px;
        padding-left: 0px;
      } 
      
      body {
        width: 420mm;
        margin: 0px;
        padding: 0px;
      }
    </style>
</head>
<body>
  <div style="padding: 20px">
  Neraca Lajur Bulan {{ $bulan }}

  <br>
  <br>

  <table border="1" cellspacing="0" cellpadding="6" style="width: 100%">
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
          <td>{{ $item['saldo']['kode'] }}</td>
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
        <td>Laba Rugi</td>
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
      <tr>
        <td></td>
        <td></td>
      @foreach ($split as $key => $saldo)
        @php
          $debet = collect($saldo)->sum('debet');
          $kredit = collect($saldo)->sum('kredit');  
        @endphp
        @if ($debet == $kredit)
        <td>{{ format_uang($debet) }}</td>
        <td>{{ format_uang($kredit) }}</td>
        @elseif ($debet > $kredit)
        <td>{{ format_uang($debet) }}</td>
        <td>{{ (($debet - $kredit) + $kredit)  != 0 ? format_uang($debet - $kredit + $kredit)  : '' }}</td>
        @elseif ($debet < $kredit)
        <td>{{ (($kredit - $debet) + $debet) != 0 ? format_uang($kredit - $debet  + $debet) : '' }}</td>
        <td>{{ format_uang($kredit) }}</td>
        @endif
      @endforeach
      </tr>
    </tbody>
  </table>
  </div>
</body>
</html>