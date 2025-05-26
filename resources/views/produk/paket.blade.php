<x-app-layout>
  <x-slot name="header">
    {{ __('Paket Produk') }}
  </x-slot>

  <div class="p-4 mx-auto max-w-screen-xl md:p-6">
    <div class="grid grid-cols-1">
      <!-- Breadcrumb Start -->
      <div x-data="{ 
        pageName: `Add Paket Produk`,
        urls:[
          {name: 'Produk', url: '{{ route('produk') }}'},
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
          
          <form class="" action="{{ route('produk.paket.store',$produk->id) }}" method="POST">
            @csrf

            <template x-for="(item, index) in items" :key="index">
              <div class="mb-6 space-y-2">
                <!-- Produk & Jumlah -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-4">
                  <!-- Produk -->
                  <div class="flex-1">
                    <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Produk</label>
                    <select
                      :name="`produk_ids[]`"
                      x-model="item.produk_id"
                      :class="messages[`produk_ids.${index}`]
                        ? 'border-error-300 dark:border-error-700 focus:border-error-300 focus:ring-error-500/10'
                        : 'border-gray-300 dark:border-gray-700 focus:border-brand-300 focus:ring-brand-500/10'"
                      class="h-11 w-full rounded-lg bg-white dark:bg-gray-900 text-sm px-4 py-2.5 text-gray-800 dark:text-white/90"
                    >
                      <option value="">Pilih Produk</option>
                      <template x-for="produk in produkList.filter(p => 
                        !items.some(i => i.produk_id == p.id && i !== item)
                      )" :key="produk.id">
                      <option :selected="produk.id == item.produk_id" :value="produk.id" x-text="produk.nama"></option>
                    </template>
                    </select>
                    <template x-if="messages[`produk_ids.${index}`]">
                      <p class="text-sm text-error-500 mt-1" x-text="messages[`produk_ids.${index}`][0]"></p>
                    </template>
                  </div>
            
                  <!-- Jumlah -->
                  <div class="w-full sm:w-32 mt-4 sm:mt-0">
                    <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah</label>
                    <input type="number" min="1"
                      :name="`jumlahs[]`"
                      x-model="item.jumlah"
                      :class="messages[`jumlahs.${index}`]
                        ? 'border-error-300 dark:border-error-700 focus:border-error-300 focus:ring-error-500/10'
                        : 'border-gray-300 dark:border-gray-700 focus:border-brand-300 focus:ring-brand-500/10'"
                      class="h-11 w-full rounded-lg bg-white dark:bg-gray-900 text-sm px-4 py-2 text-center text-gray-800 dark:text-white/90"
                    />
                    <template x-if="messages[`jumlahs.${index}`]">
                      <p class="text-sm text-error-500 mt-1" x-text="messages[`jumlahs.${index}`][0]"></p>
                    </template>
                  </div>
            
                  <!-- Tombol Hapus -->
                  <div class="mt-4 sm:mt-6 sm:ml-2">
                    <div class="sm:w-full flex justify-end">
                      <button type="button" @click="removeItem(index)"
                      class="text-red-500 text-xl hover:text-red-700">
                      X
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </template>
            

            <!-- Tombol & Total -->
            <div class="flex justify-between items-start mt-6 flex-col sm:flex-row sm:items-center sm:space-x-6">
              <div class="space-y-1 text-sm font-medium text-gray-700 dark:text-white/80">
                <div>Total Jumlah: <span x-text="totalJumlah()"></span></div>
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


