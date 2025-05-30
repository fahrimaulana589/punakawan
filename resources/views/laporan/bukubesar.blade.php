@php
    use App\Models\Akun;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Besar Bulan {{ $bulan }}</title>
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
  Buku Besar Bulan {{ $bulan }}

  <br>
  <br>
  <br>
  <br>

  @foreach ($mergedData as $index => $items)
  @php
    $akun = Akun::find($index);
    $totaldebet = 0;
    $totalkredit = 0;     
  @endphp
  <table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead>
      <tr>
        <th colspan="6" style="text-align: left;">
          Nama Akun : {{ $akun->nama }}
        </th>
      </tr>
      <tr>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Keterangan</th>
        <th rowspan="2">Debet (Rp)</th>
        <th rowspan="2">Kredit (Rp)</th>
        <th colspan="2">Saldo</th>
      </tr>
      <tr>
        <th>Debet (Rp)</th>
        <th>Kredit (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
      @php
        $total = $item['total'];
        if($item['status'] == 'debet'){
          $totaldebet += $total;
          $totalkredit -= $total;
        }else if($item['status'] == 'kredit'){
          $totaldebet -= $total;
          $totalkredit += $total;
        }
      @endphp
      <tr>
        <td>
          {{ $item['tanggal'] }}
        </td>
        <td>
          {{ $item['nama'] }}
        </td>
        @if ($item['status'] == 'debet')
        <td>
          {{ format_uang($item['total']) }}
        </td>
        <td>
          
        </td>
        @elseif ($item['status'] == 'kredit')  
        <td>
          
        </td>
        <td>
          {{ format_uang($item['total']) }}
        </td>
        @endif
        <td>
          @if ($totaldebet >= 0)
            {{ format_uang($totaldebet) }}
          @endif
        </td>
        <td>
          @if ($totalkredit >= 0)
            {{ format_uang($totalkredit) }}
          @endif
        </td>
      </tr>  
      @endforeach
    </tbody>
  </table>
  <br>
  <br>
  <br>
  <br>  
  @endforeach
  
</body>
</html>