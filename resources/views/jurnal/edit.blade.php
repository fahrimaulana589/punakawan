<x-app-layout>
  <x-slot name="header">
    {{ __('Edit Jurnal') }}
  </x-slot>
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Edit Jurnal`,
        urls:[
          {name: 'Jurnal', url: '{{ route('jurnal') }}'},
        ]
      }">
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

      <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <div class="px-5 py-4 sm:px-6 sm:py-5">
          <h3
            class="text-base font-medium text-gray-800 dark:text-white/90"
          >
            Data Jurnal
          </h3>
        </div>
        <div class="grid grid-cols-12 border border-gray-100 dark:border-gray-800">
          <form
            class="space-y-6 col-span-12 lg:col-span-7 md:col-span-8  p-5 sm:p-6"
            action="{{ route('jurnal.update', $jurnal->id) }}"
            method="POST"
          >
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tanggal
              </label>
              <input
                type="date"
                name="tanggal"
                value="{{ old('tanggal', $jurnal->tanggal) }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              />
              @error('tanggal')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
              @enderror
            </div>

            <!-- Debet -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Debet
              </label>
              <select
                name="debet_id"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              >
                <option value="">Select Option</option>
                @foreach ($akuns as $akun)
                  <option value="{{ $akun->id }}" {{ old('debet_id', $jurnal->debet_id) == $akun->id ? 'selected' : '' }}>
                    {{ $akun->nama }}
                  </option>
                @endforeach
              </select>
              @error('debet_id')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
              @enderror
            </div>

            <!-- Kredit -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Kredit
              </label>
              <select
                name="kredit_id"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              >
                <option value="">Select Option</option>
                @foreach ($akuns as $akun)
                  <option value="{{ $akun->id }}" {{ old('kredit_id', $jurnal->kredit_id) == $akun->id ? 'selected' : '' }}>
                    {{ $akun->nama }}
                  </option>
                @endforeach
              </select>
              @error('kredit_id')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
              @enderror
            </div>

            <!-- Nama -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama
              </label>
              <input
                type="text"
                name="nama"
                value="{{ old('nama', $jurnal->nama) }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              />
              @error('nama')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
              @enderror
            </div>

            <!-- Total -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Total
              </label>
              <input
                type="number"
                name="total"
                value="{{ old('total', $jurnal->total) }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              />
              @error('total')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
              @enderror
            </div>

            <div class="flex items-center justify-end">
              <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              >
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
