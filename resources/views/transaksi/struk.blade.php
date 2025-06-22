<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Struk</title>
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
        {{ $transaksi->kode }}
      </div>
      <div>
        {{ $transaksi->tanggal }}
      </div>
    </div>
    <div class="text-sm">
      {{ $transaksi->karyawan->nama }}
    </div>
  </div>
  
  <div>
    <div class="flex flex-col gap-2">
      @foreach ($transaksi->penjualan as $item)
      <div class="text-sm break-words">
        <div class="flex justify-between text-sm">
          <div>
            {{ $item->jumlah }} x {{ $item->produk->harga }}
          </div>
          <div>
            {{ format_uang($item->jumlah * $item->produk->harga) }}
          </div>
        </div>
        <div class="text-sm">
          {{ $item->produk->nama }}
        </div>
      </div>
      @endforeach
    </div>
  </div>

  <div class="flex justify-between text-sm">
    <div>
      TOTAL
    </div>
    <div>
      {{ $transaksi->totalRupiah }}
    </div>
  </div>

  <div >
    <div class="text-center text-sm">
      HP: {{ $profile->handphone }}
    </div>
    <div class="text-center text-xl">
      TERIMAKASIH
    </div>
  </div>
</body>
</html>