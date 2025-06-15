<x-app-layout>
  <x-slot name="header">
    {{ __('Laporan') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ pageName: `Laporan`}">
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
                        Tahun
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Bulan
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Update Terakhir
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center justify-end">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Action
                      </p>
                    </div>
                  </th>
                </tr>
              </thead>
              <!-- table header end -->
              <!-- table body start -->
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($laporans as $laporan)
                  <tr>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $laporan->tahun }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $laporan->namaBulan }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $laporan->updated_at->format('d M Y') }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center justify-end mb-4 gap-2">
                        <a
                        href="{{ route('laporan.hpp', $laporan->id) }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                          Show
                        </a>
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
      {{ $laporans->links() }}
    </div>
  </div>
</x-app-layout>

