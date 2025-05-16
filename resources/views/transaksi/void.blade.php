<x-app-layout>
  <x-slot name="header">
    {{ __('Transaksi') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ pageName: `Transaksi`}">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <!-- ====== Table Six Start -->
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
                      Tanggal
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Kasir
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Total
                    </p>
                  </div>
                </th>
                <th class="px-5 py-3 sm:px-6">
                  <div class="flex items-center">
                    <p
                      class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                    >
                      Status
                    </p>
                  </div>
                </th>
              </tr>
            </thead>
            <!-- table header end -->
            <!-- table body start -->
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              @foreach ($transaksis as $transaksi)
                <tr>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->tanggal }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->pegawai->nama }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        Rp. {{ $transaksi->total }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->status }}
                      </p>
                    </div>
                  </td>  
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!-- ====== Table Six End -->
    </div>

    <div class="mt-4">
      {{-- {{ $transaksis->links() }}  --}}
    </div>
  </div>
</x-app-layout>

