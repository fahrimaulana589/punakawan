<x-app-layout>
  <x-slot name="header">
    {{ __('Transaksi') }}
  </x-slot>

  <div class="p-4 mx-auto max-w-screen-xl md:p-6">
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Add Penjualan`,
        urls:[
          {name: 'Transaksi', url: '{{ route('penjualan') }}'},
        ]
      }">
        @include('partials.breadcrumb')
      </div>
      <!-- Breadcrumb End -->

      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-5 py-4 sm:px-6 sm:py-5">
          <h3
            class="text-base font-medium text-gray-800 dark:text-white/90"
          >
            Data Pegawai
          </h3>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4" 
          x-data="{
            items: {{ $old }},
            messages : {{ $messages }} ,
            produkList: @js($produks),
            getHarga(id) {
              const p = this.produkList.find(pr => pr.id == id);
              return p ? p.harga : 0;
            },
            totalJumlah() {
              return this.items.reduce((sum, i) => sum + Number(i.jumlah || 0), 0);
            },
            totalHarga() {
              return this.items.reduce((sum, i) => {
                const harga = this.getHarga(i.produk_id);
                return sum + (harga * Number(i.jumlah || 0));
              }, 0);
            },
            formatRupiah(angka) {
              return 'Rp ' + angka.toLocaleString('id-ID');
            },
            addItem() {
              this.items.push({ produk_id: '', jumlah: 1 });
            },
            removeItem(index) {
              this.items.splice(index, 1);
            }
          }">
          
          <form class="" action="{{ route('penjualan.store') }}" method="POST">
            @csrf

            <template x-for="(item, index) in items" :key="index">
              <div class="mb-6 space-y-6">
                <div>
                  <div class="flex items-center space-x-4">
                    <!-- Produk Select -->
                    <div class="flex-1">
                      <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400"
                      >Produk</label>
                      <select
                        :name="`produk_ids[]`"
                        :class="messages[`produk_ids.${index}`]
                          ? 'dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'
                          : 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'"
                         x-model="item.produk_id"
                      >
                        <option value="">Pilih Produk</option>
                        <template x-for="produk in produkList.filter(p => 
                          !items.some(i => i.produk_id == p.id && i !== item)
                        )" :key="produk.id">
                          <option :selected="produk.id == item.produk_id" :value="produk.id" x-text="produk.nama"></option>
                        </template>
                      </select>
                    </div>
  
                    <!-- Tombol Hapus (X) -->
                    <button type="button" @click="removeItem(index)" 
                      class="text-red-500 text-xl hover:text-red-700">
                      X
                    </button>
                    
                  </div>

                  <template x-if="messages[`produk_ids.${index}`]">
                    <p class="text-theme-xs text-error-500" x-text="messages[`produk_ids.${index}`][0]"></p>
                  </template>
                </div>
                
                <div>
                  <div class="grid grid-cols-2 gap-4">
                    <!-- Harga -->
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Harga</label>
                      <input type="text"
                        :value="formatRupiah(getHarga(item.produk_id))"
                        :class="messages[`jumlahs.${index}`]
                          ? 'dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'
                          : 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'"
                        readonly />
                    </div>
  
                    <!-- Jumlah -->
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah</label>
                      <input type="number" min="1"
                        :name="`jumlahs[]`"
                        x-model="item.jumlah"
                        :class="messages[`jumlahs.${index}`]
                          ? 'dark:bg-dark-900 border-error-300 shadow-theme-xs focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800 w-full rounded-lg border bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'
                          : 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'"
                        />
                    </div>
                  </div>  
                  <template x-if="messages[`jumlahs.${index}`]">
                    <p class="text-theme-xs text-error-500">
                      <span x-text="messages[`jumlahs.${index}`][0]"></span>
                    </p>
                  </template>
                </div>
                <!-- Harga & Jumlah Row -->
                
              </div>
            </template>

            <!-- Tombol & Total -->
            <div class="flex justify-between items-start mt-6 flex-col sm:flex-row sm:items-center sm:space-x-6">
              <div class="space-y-1 text-sm font-medium text-gray-700 dark:text-white/80">
                <div>Total Jumlah: <span x-text="totalJumlah()"></span></div>
                <div>Total Harga: <span x-text="formatRupiah(totalHarga())"></span></div>
              </div>

              <div class="flex gap-2 mt-4 sm:mt-0 w-full sm:w-0.5 md:w-0 justify-end">
                <button type="button" @click="addItem"
                  class="inline-flex items-center gap-2 rounded-lg bg-yellow-600 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-yellow-700">
                  Lainnya
                </button>

                <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                  Simpan
                </button>
              </div>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>


