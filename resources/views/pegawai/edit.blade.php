<x-app-layout>
  <x-slot name="header">
    {{ __('Edit Pegawai') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Edit Pegawai`,
        urls:[
          {name: 'Pegawai', url: '{{ route('pegawai') }}'},
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
            Data Pegawai
          </h3>
        </div>
        <div class="grid grid-cols-12 border border-gray-100 dark:border-gray-800">
          <form
            class="space-y-6 col-span-12 lg:col-span-7 md:col-span-8  p-5 sm:p-6"
            action="{{ route('pegawai.update',$pegawai->id) }}"
            method="POST"
          >
            @csrf
            @method('PUT')

            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Kode
              </label>
              <input
                type="text"
                name="kode"
                value="{{ old('kode',$pegawai->kode) }}"
                readonly
                @error('kode')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
              />
              @error('kode')
                <p class="text-theme-xs text-error-500">
                  {{ $message }}
                </p>
              @enderror
            </div>


            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Nama
              </label>
              <input
                type="text"
                name="nama"
                value="{{ old('nama',$pegawai->nama) }}"
                @error('nama')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
              />
              @error('nama')
                <p class="text-theme-xs text-error-500">
                  {{ $message }}
                </p>
              @enderror
            </div>

             <!-- Elements -->
             <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Jabatan
              </label>
              <input
                type="text"
                name="jabatan"
                value="{{ old('jabatan',$pegawai->jabatan) }}"
                @error('jabatan')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
              />
              @error('jabatan')
                <p class="text-theme-xs text-error-500">
                  {{ $message }}
                </p>
              @enderror
            </div>

             <!-- Elements -->
             <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Alamat
              </label>
              <textarea
                placeholder="Enter a description..."
                type="text"
                rows="6"
                name="alamat"
                @error('alamat')
                  class="dark:bg-dark-900 shadow-theme-xs border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
              >{{ old('alamat',$pegawai->alamat) }}</textarea>
              @error('alamat')
                <p class="text-theme-xs text-error-500">
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Handphone
              </label>
              <input
                type="text"
                name="no_hp"
                value="{{ old('no_hp',$pegawai->no_hp) }}"
                @error('no_hp')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
                />
                @error('no_hp')
                  <p class="text-theme-xs text-error-500">
                    {{ $message }}
                  </p>
                @enderror
            </div>

            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Jenis Kelamin
              </label>
              <div
                x-data="{ isOptionSelected: false }"
                class="relative z-20 bg-transparent"
              >
                <select
                  @error('jenis_kelamin')
                    class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @else
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @enderror:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                  name="jenis_kelamin"
                  @change="isOptionSelected = true"
                >
                  <option
                    value=""
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Select Option
                  </option>
                  <option
                    value="L"
                    @if (old('jenis_kelamin',$pegawai->jenis_kelamin) == 'L')
                      selected
                    @endif
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Laki laki
                  </option>
                  <option
                    value="P"
                    @if (old('jenis_kelamin',$pegawai->jenis_kelamin) == 'P')
                      selected
                    @endif
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Perempuan
                  </option>
                </select>
                <span
                  class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                >
                  <svg
                    class="stroke-current"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                      stroke=""
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                </span>
              </div>
              @error('jenis_kelamin')
                <p class="text-theme-xs text-error-500">
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Gaji
              </label>
              <input
                type="text"
                name="gaji"
                value="{{ old('gaji',$pegawai->gaji) }}"
                @error('gaji')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
              />
              @error('gaji')
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

