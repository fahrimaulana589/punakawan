<x-app-layout>
  <x-slot name="header">
    {{ __('Show Transaksi') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Show Transaksi`,
        urls:[
          {name: 'Transaksi', url: '{{ url()->previous() }}'},
        ]
      }">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <div class="grid grid-cols-12"> 
        
        
        <div class="col-span-12 lg:col-span-4 md:col-span-6 sm:col-span-12 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="bg-white shadow-lg rounded-2xl p-6 font-mono">
            <div class="text-center border-b pb-4">
              <p class="text-sm text-gray-500">Tanggal: {{ $transaksi->tanggal }}</p>
              <p class="text-sm text-gray-500">Kasir: {{ $transaksi->pegawai->nama }}</p>
            </div>
          
            <div class="mt-4">
              <h2 class="text-lg font-semibold mb-2">Produk</h2>
              <div class="space-y-2">
                @foreach ($transaksi->penjualan as $item)
                  <div class="flex justify-between text-sm border-b pb-1">
                    <span>{{ $item->jumlah }}  x  ({{ $item->produk->nama }})</span>
                    <span>{{ format_uang($item->jumlah * $item->produk->harga) }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          
            <div class="mt-4 pt-4 flex justify-between font-semibold text-base">
              <span>Total</span>
              <span>{{ $transaksi->totalRupiah }}</span>
            </div>
          
          </div>        
        </div>
      </div>
     
      
      
    </div>
  </div>
</x-app-layout>

