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

      <div x-data="{
        items: {{ $old }},
        messages: {{ $messages }},
        produkList: @js($produks),
        originalProdukList: @js($produks),
    
        getProduk(id) {
            return this.produkList.find(p => p.id == id);
        },
    
        getNamaProduk(p) {
            return `Stok: ${p.stok.toString().padStart(3, '0')} - ${p.nama}`;
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
            // Reset produk list
            this.produkList = JSON.parse(JSON.stringify(this.originalProdukList));
    
            // Kurangi stok produk yang dipilih
            this.items.forEach(item => {
                const produk = this.getProduk(item.produk_id);
                const jumlah = Number(item.jumlah || 0);
                if (!produk || !jumlah) return;
    
                // Kurangi stok produk utama
                const pIndex = this.produkList.findIndex(p => p.id === produk.id);
                if (pIndex >= 0) this.produkList[pIndex].stok -= jumlah;
    
                
                if (produk.parent && Array.isArray(produk.parent)) {
                    const childrenMap = new Map(); // key: id, value: produk anak
                
                    produk.parent.forEach(rel => {
                        const parentIndex = this.produkList.findIndex(p => p.id === rel.id);
                        
                        if (parentIndex >= 0) {
                            const parentProduk = this.produkList[parentIndex];
                
                            if (Array.isArray(parentProduk.children)) {
                                parentProduk.children.forEach(child => {
                                    // Masukkan hanya jika belum ada
                                    if (!childrenMap.has(child.id)) {
                                        childrenMap.set(child.id, child);
                                    }
                                });
                            }
                
                            // Kurangi stok parent
                            this.produkList[parentIndex].stok -= jumlah * rel.pivot.jumlah;
                        }
                    });
                
                    // Ambil array dari nilai anak-anak unik
                    const children = Array.from(childrenMap.values());

                    //update stok produk anak
                    children.forEach(child => {
                        const childIndex = this.produkList.findIndex(p => p.id === child.id);
                        const chd = this.produkList[childIndex];

                        const stok = [];
                        
                        chd.parent.forEach(rel => {
                            const parentIndex = this.produkList.findIndex(p => p.id === rel.id);
                            const stokasli = this.produkList[parentIndex].stok;
                            const jumlahyangdibutuhkan = rel.pivot.jumlah;
                            const ketersedian = Math.floor(stokasli / jumlahyangdibutuhkan);
                           
                            stok.push(ketersedian);
                        });

                        // Ambil nilai stok terkecil dari semua parent
                        const stokMinimum = Math.min(...stok);

                        // Update stok produk anak
                        this.produkList[childIndex].stok = stokMinimum;
                    });
              }
              
            });
    
            // Update stok produk lain agar tidak bisa dipilih kalau stoknya habis
            this.produkList.forEach(p => {
                p.stok = p.stok;
            });

        },
    
        init() {
            this.recalculateStok();
        }
    }">
        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
    
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
                                            :disabled="produk.stok <= 0 && item.produk_id != produk.id"
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


