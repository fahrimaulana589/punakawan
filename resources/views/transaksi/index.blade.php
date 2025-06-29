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

      @session('success')
      <div
          class="mb-4 rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15"
        >
          <div class="flex items-start gap-3">
            <div class="-mt-0.5 text-success-500">
              <svg
                class="fill-current"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z"
                  fill=""
                />
              </svg>
            </div>

            <div>
              <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                Success Message
              </h4>

              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ session('success') }}
              </p>
            </div>
          </div>
      </div>
      @endsession

      @session('error')
      <div
        class="mb-4 rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15"
      >
        <div class="flex items-start gap-3">
          <div class="-mt-0.5 text-error-500">
            <svg
              class="fill-current"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z"
                fill="#F04438"
              />
            </svg>
          </div>

          <div>
            <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
              Error Message
            </h4>

            <p class="text-sm text-gray-500 dark:text-gray-400">
               {{ session('error') }}
            </p>
          </div>
        </div>
      </div>
        

      @endsession
      
      
      @can('transaksi_kasir')
      <div class="flex items-center justify-end mb-4">
        <a href="{{ route('penjualan.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Add Transaksi
        </a>
      </div>
      @endcan

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
                      kode
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
                      Karyawan
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
                      Show
                      </a>
                      @can('transaksi_kasir')
                      <form id="finish-form-{{ $transaksi->id }}" action="{{ route('penjualan.finish', $transaksi->id) }}" method="POST" class="inline">
                        @csrf
                        <button 
                            x-on:click.prevent="
                                window.Swal.fire({
                                    title: 'Yakin ingin menyelesaikan transaksi?',
                                    text: 'Tindakan ini tidak dapat dibatalkan!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Ya, selesaikan!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('finish-form-{{ $transaksi->id }}').submit();
                                    }
                                });
                            " 
                            type="button"
                            class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                        >
                            Selsesai
                        </button>
                      </form>
                      <form id="cancel-form-{{ $transaksi->id }}" action="{{ route('penjualan.cancel', $transaksi->id) }}" method="POST" class="inline">
                        @csrf
                        <button 
                            x-on:click.prevent="
                                window.Swal.fire({
                                    title: 'Yakin ingin membatalkan transaksi?',
                                    text: 'Tindakan ini tidak dapat dibatalkan!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Ya, batalkan!',
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
                            Batal
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

