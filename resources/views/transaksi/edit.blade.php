<x-app-layout>
  <x-slot name="header">
    {{ __('Transaksi') }}
  </x-slot>

  <div class="p-4 mx-auto max-w-screen-xl md:p-6">
    <div class="grid grid-cols-1">
        <!-- Breadcrumb Start -->
        <div x-data="{ 
            pageName: `Edit Transaksi`,
            urls:[
            {name: 'Transaksi', url: '{{ route('penjualan.riwayat') }}'},
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

        @error('produk_ids')
        
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
                Transaksi updated failed.
                </p>
            </div>
            </div>
        </div>
        
        @enderror

      <div x-data="{
        items: {{ $old }},
        messages: {{ $messages }},
        produkList: @js($produks),
        originalProdukList: @js($produks),
    
        getProduk(id) {
            return this.produkList.find(p => p.id == id);
        },
    
        getNamaProduk(p) {
            return `${p.nama}`;
        },
    
        getHarga(id) {
            const p = this.getProduk(id);
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
            this.recalculateStok();
        },
    
        recalculateStok() {
            

        },
    
        init() {
            this.recalculateStok();
        }
    }">
        <form action="{{ route('penjualan.update',$transaksi->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Elements -->
            <div class="mb-6">
                <label
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                    Tanggal
                </label>

                <div class="relative">
                <input
                  type="date"
                  name="tanggal"
                  value="{{ old('tanggal',$transaksi->tanggal) }}"
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

            <!-- Elements -->
            <div class="mb-6">
              <label
                class=" mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Total
              </label>
              <input
                type="number"
                name="total_transaksi"
                value="{{ $transaksi->total }}"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                readonly
              />
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="mb-6 space-y-6">
                    <div>
                        <div class="flex items-center space-x-4">
                            <!-- Produk Select -->
                            <div class="flex-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Produk</label>
                                <select
                                    :name="`produk_ids[]`"
                                    x-model="item.produk_id"
                                    @change="item.jumlah = 1;recalculateStok()"
                                    :class="messages[`produk_ids.${index}`]
                                        ? 'border-error-300'
                                        : ''"
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm text-gray-800 dark:bg-gray-900 dark:text-white/90"
                                >
                                    <option value="">Pilih Produk</option>
                                    <template x-for="produk in produkList.filter(p => !items.some(i => i.produk_id == p.id && i !== item))" :key="produk.id">
                                        <option 
                                            :value="produk.id" 
                                            x-text="getNamaProduk(produk)"
                                            :selected="item.produk_id == produk.id"
                                        ></option>
                                    </template>
                                </select>
                            </div>
    
                            <!-- Tombol Hapus -->
                            <button type="button" @click="removeItem(index)" class="text-red-500 text-xl hover:text-red-700">X</button>
                        </div>
                        <template x-if="messages[`produk_ids.${index}`]">
                            <p class="text-theme-xs text-error-500" x-text="messages[`produk_ids.${index}`][0]"></p>
                        </template>
                    </div>
    
                    <!-- Harga & Jumlah -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Harga</label>
                            <input type="text" :value="formatRupiah(getHarga(item.produk_id))" readonly
                                class="w-full rounded-lg border px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white/90" />
                        </div>
    
                        <div>
                            <div x-show="item.produk_id" x-transition>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah</label>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="px-3 py-2 text-sm font-bold border rounded-lg text-gray-700 dark:text-white disabled:opacity-50"
                                        @click="
                                            if (item.jumlah > 1) item.jumlah--;
                                            recalculateStok();
                                        "
                                    >-</button>
                            
                                    <input type="number" min="1" x-model="item.jumlah" @input="recalculateStok()"
                                        :name="`jumlahs[]`"
                                        readonly
                                        class="w-full rounded-lg border px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white/90 text-center" />
                            
                                    <button
                                        type="button"
                                        class="px-3 py-2 text-sm font-bold border rounded-lg text-gray-700 dark:text-white disabled:opacity-50"
                                        @click="
                                            const produk = getProduk(item.produk_id);
                                           
                                            if (produk && 0 < produk.stok) {
                                                item.jumlah++;
                                                recalculateStok();
                                            }
                                        "
                                    >
                                        +
                                    </button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Total -->
            <div class="flex justify-between items-start mt-6 flex-col sm:flex-row sm:items-center sm:space-x-6">
                <div class="space-y-1 text-sm font-medium text-gray-700 dark:text-white/80">
                    <div>Total Jumlah: <span x-text="totalJumlah()"></span></div>
                    <div>Total Harga: <span x-text="formatRupiah(totalHarga())"></span></div>
                </div>
    
                <!-- Tombol -->
                <div class="flex gap-2 mt-4 sm:mt-0 w-full sm:w-0.5 md:w-0 justify-end">
                    <button type="button" @click="addItem"
                        class="inline-flex items-center gap-2 rounded-lg bg-yellow-600 px-5 py-3.5 text-sm font-medium text-white">
                        Tambah
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
      </div>

    </div>
  </div>
</x-app-layout>