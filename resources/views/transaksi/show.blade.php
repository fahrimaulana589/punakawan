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
        <div class="col-span-12 lg:col-span-5 md:col-span-7 sm:col-span-12 rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
          <div class="bg-white dark:bg-white/[0.03] shadow-lg rounded-2xl p-6 font-mono">
            <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4">
              <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal: {{ $transaksi->tanggal }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Kasir: {{ $transaksi->karyawan->nama }}</p>
            </div>
          
            <div class="mt-4">
              <h2 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">Produk</h2>
              <div class="space-y-2">
          @foreach ($transaksi->penjualan as $item)
            <div class="flex justify-between text-sm border-b border-gray-100 dark:border-gray-800 pb-1">
              <span class="text-gray-700 dark:text-gray-200">{{ $item->jumlah }}  x  ({{ $item->produk->nama }})</span>
              <span class="text-gray-700 dark:text-gray-200">{{ format_uang($item->jumlah * $item->produk->harga) }}</span>
            </div>
          @endforeach
              </div>
            </div>
          
            <div class="mt-4 pt-4 flex justify-between font-semibold text-base">
              <span class="text-gray-800 dark:text-gray-100">Total</span>
              <span class="text-gray-800 dark:text-gray-100">{{ $transaksi->totalRupiah }}</span>
            </div>

            <div class="mt-6 flex justify-end">
              <a href="{{ route('penjualan.struk', $transaksi->id) }}" target="_blank"
                 class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m-4-5v9"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20 12V7a2 2 0 00-2-2H6a2 2 0 00-2 2v5"/>
                </svg>
                Cetak Struk
              </a>
            </div>
          
          </div>        
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

