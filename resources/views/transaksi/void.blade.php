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

      @include('partials.filter',['paginate' => true])

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
                      Kode
                    </p>
                  </div>
                </th>
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
              @foreach ($transaksis as $transaksi)
                <tr>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->kode }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->tanggalFormat }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->karyawan->nama }}
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $transaksi->totalRupiah }}
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
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-end">  
                      <a
                      href="{{ route('penjualan.show',$transaksi->id) }}"
                      class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      >
                        show
                      </a>
                      
                      @can('transaksi_delete')        
                      <form id="cancel-form-{{ $transaksi->id }}" action="{{ route('penjualan.destroy', $transaksi->id) }}" method="POST" class="inline">
                        @csrf
                        <button 
                            x-on:click.prevent="
                                window.Swal.fire({
                                    title: 'Yakin ingin menghapus transaksi?',
                                    text: 'Tindakan ini tidak dapat dibatalkan!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Ya, hapus!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('cancel-form-{{ $transaksi->id }}').submit();
                                    }
                                });
                            " 
                            type="button"
                            class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Hapus
                        </button>
                      </form>   
                      @endcan                   
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
      {{ $transaksis->links() }} 
    </div>
  </div>
</x-app-layout>

