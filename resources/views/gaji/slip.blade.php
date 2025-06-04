<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Slip Gaji</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body {
      max-width: 58mm;
    }
  </style>
</head>
<body class="px-2 py-3 flex flex-col gap-3">
  <div>
    <div class="text-center text-xl">
      {{ $profile->nama }}
    </div>

    <div class="text-center">
      {{ $profile->alamat }}
    </div>
  </div>
  
  <div>
    <div class="flex justify-between text-sm">
      <div>
        {{ $slipgaji["tanggal_awal"] }} - {{ $slipgaji["tanggal_akhir"] }}
      </div>
      <div>
      </div>
    </div>
  </div>
  
  <div class="flex flex-col">
    <div class="text-sm break-words">
      <div class="flex justify-between text-sm">
        <div>
          Nama
        </div>
        <div>
          {{ $slipgaji["nama"] }}
        </div>
      </div>
    </div>
    <div class="text-sm break-words">
      <div class="flex justify-between text-sm">
        <div>
          Kehadiran
        </div>
        <div>
          {{ $slipgaji["hari_kerja"] ?? 0 }} Hari
        </div>
      </div>
    </div>
  </div>  

  <div class="flex flex-col gap-1">
    <div class="text-sm break-words">
      <div class="flex justify-between text-sm">
        <div>
          {{ ($slipgaji["hari_kerja"] ?? 0) }} X {{ ($slipgaji["gaji_pokok"] ?? 0) }}
        </div>
        <div>
          {{ format_uang(($slipgaji["gaji_pokok"] ?? 0) * ($slipgaji["hari_kerja"] ?? 0)) }}
        </div>
      </div>
      <div>
        Gaji
      </div>
    </div>

    @foreach ($slipgaji['lainya'] as $item)
      @if (($item['type'] ?? '') !== 'potongan')
        <div class="text-sm break-words">
          <div class="flex justify-between text-sm">
            <div>
              {{ $slipgaji["hari_kerja"] ?? 0 }} X {{ $item['lainya_pokok'] ?? 0 }}
            </div>
            <div>
              {{ format_uang(($item['lainya_pokok'] ?? 0) * ($slipgaji["hari_kerja"] ?? 0)) }}
            </div>
          </div>
          <div>
              {{ $item['nama'] ?? '-' }}
          </div>  
        </div>
      @endif
    @endforeach
  </div>  


  <div class="flex flex-col">
    @if (collect($slipgaji['lainya'])->where('type', 'potongan')->count() > 1)
      <div>Potongan</div>
    @endif
    @foreach ($slipgaji['lainya'] as $item)
      @if (($item['type'] ?? '') == 'potongan')
        <div class="text-sm break-words">
          <div class="flex justify-between text-sm">
          <div>
            {{ $item['nama'] ?? '-' }}
          </div>
          <div>
            {{ $item['lainya_pokok'] ?? 0 }}
          </div>
          </div>
        </div>
      @endif
    @endforeach
  </div>

  <div class="flex flex-col">
    <div class="text-sm break-words">
      <div class="flex justify-between text-sm">
        <div>
          Total Gaji
        </div>
        <div>
          {{ $slipgaji["total"]}}
        </div>
      </div>
    </div>
  </div>

</body>
</html>