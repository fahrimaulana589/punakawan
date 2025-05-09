<x-app-layout>
  <x-slot name="header">
    {{ __('Pegawai') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ pageName: `Pegawai`}">
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
        
      <div class="flex items-center justify-end mb-4">
        <a href="{{ route('pegawai.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Add Pegawai
        </a>
      </div>
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
                        Nama
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Alamat
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Jabatan
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Jenis Kelamin
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Handphone
                      </p>
                    </div>
                  </th>
                  <th class="px-5 py-3 sm:px-6">
                    <div class="flex items-center">
                      <p
                        class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
                      >
                        Gaji
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
                @foreach ($pegawais as $pegawai)
                  <tr>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->kode }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->nama }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->alamat }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->jabatan }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->jenis_kelamin }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->no_hp }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                          {{ $pegawai->gaji }}
                        </p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center justify-end mb-4">
                        <a
                        href="{{ route('pegawai.edit',$pegawai->id) }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      >
                        Edit
                      </a>
                      <form action="{{ route('pegawai.delete', $pegawai->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <a href="{{ route('pegawai.delete', $pegawai->id) }}" data-confirm-delete="true" type="submit" class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                          Delete
                        </a>
                      </form>
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
      {{ $pegawais->links() }}
    </div>
  </div>
</x-app-layout>

