  <x-app-layout>
  <x-slot name="header">
    {{ __('Show Gaji') }}
  </x-slot>
  
  
  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Show Gaji`,
        urls:[
          {name: 'Gaji', url: '{{ route('gaji') }}'},
        ]
      }">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]" 
        x-data="{
          tanggal : '{{ old('tanggal',$gaji->tanggal) }}'
        }"
      >
       <form 
            id = "generate"
            action="{{ route('gaji.generate') }}"
            method="GET"
            >
            <input type="hidden" name="tanggal" x-model="tanggal">
        </form>
        <div class="px-5 py-4 sm:px-6 sm:py-5">
          <h3
            class="text-base font-medium text-gray-800 dark:text-white/90"
          >
            Data Gaji
          </h3>
        </div>
        <div class="grid grid-cols-12 border border-gray-100 dark:border-gray-800">
          <form
            class="space-y-6 col-span-12 lg:col-span-7 md:col-span-8  p-5 sm:p-6"
            action="{{ route('gaji.store.generate') }}"
            method="POST"
          >
            @csrf

             <!-- Elements -->
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Tanggal
              </label>

              <div class="relative">
                <input
                  type="date"
                  name="tanggal"
                  readonly
                  x-model="tanggal"
                  placeholder="Select date"
                  @error('tanggal')
                    class="dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @else
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                  @enderror:class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                  onclick="this.showPicker()"
                />
                <span
                  class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                >
                  <svg
                    class="fill-current"
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                      fill=""
                    />
                  </svg>
                </span>
              </div>

              @error('tanggal')
              <p class="text-theme-xs text-error-500">
                {{ $message }}
              </p>
            @enderror
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ $tanggal_awal }} - {{ $tanggal_akhir }}
            </div>

            <div class="grid grid-cols-12 gap-2">
              @foreach($gaji->karyawans as $karyawan)
              
                <div class="dark:text-white rounded-sm p-2 col-span-12 md:col-span-6 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                  <div class="text-sm">
                    {{ $karyawan->karyawan->nama }}
                  </div>
                  <div class="text-sm">
                    {{ $karyawan->karyawan->jabatan }}
                  </div>
                  <div 
                    x-data="{ isBelow: false }" 
                    x-init="
                      $nextTick(() => {
                          const textEl = $refs.text;
                          const priceEl = $refs.price;
                          isBelow = priceEl.offsetTop > textEl.offsetTop;
                      })
                    " 
                    class="flex flex-wrap justify-between gap-x-4"
                  >
                    <div x-ref="text" class="truncate text-sm">
                      Kehadiran
                    </div>
                    <div 
                      x-ref="price" 
                      :class="isBelow ? 'w-full flex justify-end' : ''" 
                      class="text-sm"
                    >
                      {{ $rekapHadir[$karyawan->karyawan->id] ?? '0' }}
                    </div>
                  </div>
                  <div 
                    x-data="{ isBelow: false }" 
                    x-init="
                      $nextTick(() => {
                          const textEl = $refs.text;
                          const priceEl = $refs.price;
                          isBelow = priceEl.offsetTop > textEl.offsetTop;
                      })
                    " 
                    class="flex flex-wrap justify-between gap-x-4"
                  >
                    <div x-ref="text" class="truncate text-sm">
                      Gaji
                    </div>
                    <div 
                      x-ref="price" 
                      :class="isBelow ? 'w-full flex justify-end' : ''" 
                      class="text-sm"
                    >
                      {{ $karyawan->gajiRupiah }}
                    </div>
                  </div>

                  @if (!$karyawan->gajiLainyas->isEmpty())
                  <div class="my-2 h-1 dark:bg-white bg-gray-500">
                  </div>
                  @php $jenis = 0 @endphp

                  @foreach($karyawan->gajiLainyas as $penggajian)
                    @if($penggajian->type == 'potongan_bulanan')
                      @php $jenis = 'Potongan' @endphp
                    @elseif($penggajian->type == 'potongan_absensi')
                      @php $jenis = 'Potongan' @endphp
                    @elseif($penggajian->type == 'tunjangan_bulanan')
                      @php $jenis = 'Tunjangan' @endphp    
                    @elseif($penggajian->type == 'tunjangan_harian')
                      @php $jenis = 'Tunjangan' @endphp    
                    @endif

                    <div 
                      x-data="{ isBelow: false }" 
                      x-init="
                        $nextTick(() => {
                            const textEl = $refs.text;
                            const priceEl = $refs.price;
                            isBelow = priceEl.offsetTop > textEl.offsetTop;
                        })
                      " 
                      class="flex flex-wrap justify-between gap-x-4"
                    >
                      <div x-ref="text" class="truncate text-sm">
                        {{ $jenis }} {{ $penggajian->nama }}
                      </div>
                      <div 
                        x-ref="price" 
                        :class="isBelow ? 'w-full flex justify-end' : ''" 
                        class="text-sm"
                      >
                        {{ $penggajian->totalRupiah }}
                      </div>
                    </div>
                  @endforeach
                  
                  <div class="my-2 h-1 dark:bg-white bg-gray-500">
                  </div>
                  
                  
                  @endif
                  <div 
                    x-data="{ isBelow: false }" 
                    x-init="
                      $nextTick(() => {
                          const textEl = $refs.text;
                          const priceEl = $refs.price;
                          isBelow = priceEl.offsetTop > textEl.offsetTop;
                      })
                    " 
                    class="flex flex-wrap justify-between gap-x-4"
                  >
                    <div x-ref="text" class="truncate text-sm">
                      Total
                    </div>
                    <div 
                      x-ref="price" 
                      :class="isBelow ? 'w-full flex justify-end' : ''" 
                      class="text-sm"
                    >
                      {{ $karyawan->totalRupiah }}
                    </div>
                  </div>
                </div>
              @endforeach

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
                readonly
                value="{{ old('total',$gaji->total) }}"
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

            <div class="flex items-center justify-end gap-2">
              <a
                href="{{ route('gaji') }}"
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              >
                Kembali
              </a>
            </div>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</x-app-layout>

