<x-app-layout>
  <x-slot name="header">
    {{ __('Produk') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ pageName: `Produk`}">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <form action="{{ route('laporan.rekap_print') }}" method="GET" class="flex items-center justify-end mb-4">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Print
        </button>
      </form>

      @include('partials.filter',['paginate' => false])

      <div
        class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <div class="max-w-full overflow-x-auto">
          <table class="min-w-full">
            <!-- table header start -->
            <thead>
              <tr class="border-b border-gray-100 dark:border-gray-800">
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Kode
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Nama
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Harga
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Terjual
                    </p>
                  </div>
                </th>
                 <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Total Penjualan
                    </p>
                  </div>
                </th>
              </tr>
            </thead>
            <!-- table header end -->
            <!-- table body start -->
            @php
              $total = 0;
            @endphp
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              @foreach ($produks as $produk)
                <tr>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $produk->kode }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $produk->nama }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $produk->hargaRupiah  }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $produk->terjual }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        @php
                          $total += $produk->terjual * $produk->harga;
                        @endphp
                        {{ format_uang($produk->terjual * $produk->harga) }}
                      </p>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="px-5 py-4 sm:px-6">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      Total
                    </p>
                  </div>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="px-5 py-4 sm:px-6">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      {{ format_uang($total) }}
                    </p>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <!-- ====== Table Six End -->
    </div>
  </div>
</x-app-layout>

