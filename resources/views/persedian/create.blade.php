<x-app-layout>
  <x-slot name="header">
    {{ __('Buat Persediaan Bahan Produksi') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Add Persediaan Bahan Produksi`,
        urls:[
          {name: 'Persediaan Bahan Produksi', url: '{{ route('persedian') }}'},
        ]
      }">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <div class="px-5 py-4 sm:px-6 sm:py-5">
          <h3
            class="text-base font-medium text-gray-800 dark:text-white/90"
          >
            Data Persediaan Bahan Produksi
          </h3>
        </div>
        <div class="grid grid-cols-12 border border-gray-100 dark:border-gray-800">
          <form
            class="space-y-6 col-span-12 lg:col-span-7 md:col-span-8  p-5 sm:p-6"
            action="{{ route('persedian.store') }}"
            method="POST"
          >
            @csrf

            <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Tahun
              </label>
              <div
                x-data="{ isOptionSelected: false }"
                class="relative z-20 bg-transparent"
              >
                <select
                  @error('tahun')
                    class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @else
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @enderror:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                  name="tahun"
                  @change="isOptionSelected = true"
                >
                  <option
                    value=""
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Select Option
                  </option>
                  @foreach ($years as $year)
                  <option
                    value="{{ $year }}"
                    @if (old('tahun') == $year)
                      selected
                    @endif
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    {{ $year }}
                  </option>
                  @endforeach
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
              @error('tahun')
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
                Bulan
              </label>
              <div
                x-data="{ isOptionSelected: false }"
                class="relative z-20 bg-transparent"
              >
                <select
                  @error('bulan')
                    class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @else
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @enderror:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                  name="bulan"
                  @change="isOptionSelected = true"
                >
                  <option
                    value=""
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Select Option
                  </option>

                  @foreach ($bulans as $index => $bulan)
                  <option
                    value="{{ $index }}"
                    @if (old('bulan') == $index)
                      selected
                    @endif
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    {{ $bulan }}
                  </option>                    
                  @endforeach

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
              @error('bulan')
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
                Bahan Produksi
              </label>
              <div
                x-data="{ isOptionSelected: false }"
                class="relative z-20 bg-transparent"
              >
                <select
                  @error('konsumsi_id')
                    class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @else
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @enderror:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                  name="konsumsi_id"
                  @change="isOptionSelected = true"
                >
                  <option
                    value=""
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    Select Option
                  </option>
                  @foreach ($bahan_produksis as $bahan_produksi)
                  
                  <option
                    value="{{ $bahan_produksi->id }}"
                    @if (old('konsumsi_id') == $bahan_produksi->id)
                      selected
                    @endif
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                  >
                    {{ $bahan_produksi->nama }}
                  </option>  
                  @endforeach
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
              @error('konsumsi_id')
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
                Total
              </label>
              <input
                type="number"
                name="total"
                value="{{ old('total') }}"
                @error('total')
                  class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @else
                  class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                @enderror
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
                Tambah
              </button>
            </div>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</x-app-layout>

