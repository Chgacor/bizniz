<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            {{ __('Kasir Bengkel (POS)') }}
        </h2>
    </x-slot>

    <div class="h-[calc(100vh-65px)] bg-gray-100 flex overflow-hidden" x-data="posSystem()">

        <div class="w-2/3 flex flex-col h-full border-r border-gray-200">
            <div class="bg-white px-4 py-3 shadow-sm z-10 shrink-0">
                <div class="relative mb-3">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchProducts()"
                           placeholder="Cari Barang / Jasa..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 text-sm shadow-sm">
                </div>

                <div class="flex p-1 space-x-1 bg-gray-100 rounded-lg">
                    <button @click="activeTab = 'goods'"
                            class="flex-1 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md focus:outline-none transition-all flex items-center justify-center"
                            :class="activeTab === 'goods' ? 'bg-white text-brand-700 shadow ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700'">
                        📦 Sparepart
                    </button>
                    <button @click="activeTab = 'service'"
                            class="flex-1 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md focus:outline-none transition-all flex items-center justify-center"
                            :class="activeTab === 'service' ? 'bg-white text-blue-600 shadow ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700'">
                        🔧 Jasa Service
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 custom-scrollbar">
                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 pb-20">
                    <div x-show="activeTab === 'service'" @click="showCustomServiceModal = true"
                         class="bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-blue-100 hover:border-blue-500 transition aspect-[4/5] group">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-500 shadow-sm mb-2 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="font-bold text-blue-700 text-center text-xs leading-tight">Input Jasa<br>Manual</h3>
                    </div>

                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                             class="bg-white rounded-lg shadow-sm border border-gray-200 hover:border-brand-400 cursor-pointer flex flex-col overflow-hidden group hover:shadow-md transition aspect-[4/5] relative">

                            <div class="h-1/2 w-full bg-gray-100 overflow-hidden relative border-b border-gray-50">
                                <template x-if="product.image_path">
                                    <img :src="'/storage/' + product.image_path" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </template>
                                <template x-if="!product.image_path">
                                    <div class="w-full h-full flex items-center justify-center font-bold text-2xl transition"
                                         :class="product.type === 'service' ? 'bg-blue-50 text-blue-300' : 'bg-orange-50 text-orange-300'">
                                        <span x-text="product.name.substring(0,2).toUpperCase()"></span>
                                    </div>
                                </template>
                                <span class="absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide shadow-sm"
                                      :class="product.type === 'service' ? 'bg-blue-100 text-blue-700' : 'bg-gray-800 text-white'"
                                      x-text="product.type === 'service' ? 'JASA' : (product.category || 'PART')">
                                </span>
                            </div>

                            <div class="p-2.5 flex flex-col flex-1 justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-xs leading-snug line-clamp-2 mb-1" x-text="product.name"></h3>
                                    <p class="text-[10px] text-gray-400 font-mono truncate" x-text="product.product_code"></p>
                                </div>
                                <div class="flex justify-between items-end mt-2">
                                    <span class="font-bold text-sm" :class="product.type === 'service' ? 'text-blue-600' : 'text-brand-700'" x-text="formatRupiah(product.sell_price)"></span>
                                    <template x-if="product.type === 'goods'">
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 border border-gray-200" x-text="product.stock_quantity + ' unit'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-1/3 bg-white flex flex-col shadow-xl z-20 h-full">
            <div class="px-5 py-4 bg-brand-900 text-white flex justify-between items-center shrink-0">
                <div>
                    <h2 class="text-base font-bold">Nota Bengkel</h2>
                    <p class="text-[10px] text-brand-200 opacity-80">Rincian Sparepart & Jasa</p>
                </div>
                <div class="bg-white text-brand-900 px-2.5 py-1 rounded-md text-xs font-bold shadow">
                    <span x-text="cartTotalQty()"></span> Item
                </div>
            </div>

            <div class="bg-gray-50 p-3 border-b border-gray-200 shrink-0">
                <template x-if="!customer">
                    <button @click="openCustomerModal()"
                            class="w-full py-2 border border-dashed border-gray-400 text-gray-600 font-bold rounded-lg hover:bg-gray-100 transition text-xs flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Pilih Pelanggan / Member
                    </button>
                </template>
                <template x-if="customer">
                    <div class="flex justify-between items-center bg-white p-2.5 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-100 rounded-full p-1.5">
                                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm" x-text="customer.name"></div>
                                <div class="text-[10px] text-gray-500" x-text="customer.phone"></div>
                            </div>
                        </div>
                        <button @click="customer = null" class="text-gray-400 hover:text-red-500 p-1" title="Hapus Pelanggan">✕</button>
                    </div>
                </template>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-4 bg-white custom-scrollbar">

                <div x-show="cartParts.length > 0">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Sparepart (Barang)
                    </h3>
                    <div class="space-y-2">
                        <template x-for="item in cartParts" :key="item.id">
                            <div class="bg-orange-50 p-2.5 rounded-lg border border-orange-100 flex justify-between items-center relative overflow-hidden group hover:border-orange-300 transition">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-500"></div>
                                <div class="flex-1 pl-3">
                                    <div class="flex justify-between items-start">
                                        <div class="font-bold text-sm text-gray-800 line-clamp-1" x-text="item.name"></div>
                                        <button @click="removeFromCart(item.id)" class="text-gray-300 hover:text-red-500 px-2 font-bold">×</button>
                                    </div>
                                    <div class="flex justify-between items-center mt-0.5">
                                        <div class="text-[10px] text-gray-500" x-text="formatRupiah(item.price) + ' x ' + item.qty"></div>
                                        <div class="font-bold text-sm text-brand-700" x-text="formatRupiah(item.price * item.qty)"></div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center ml-2 space-y-1">
                                    <button @click="updateQty(item.id, 1)" class="w-5 h-4 bg-white border border-gray-200 hover:bg-green-50 text-green-700 rounded text-[9px]">▲</button>
                                    <span class="font-bold text-xs text-gray-800" x-text="item.qty"></span>
                                    <button @click="updateQty(item.id, -1)" class="w-5 h-4 bg-white border border-gray-200 hover:bg-red-50 text-red-700 rounded text-[9px]">▼</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="cartServices.length > 0">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center pt-2 border-t border-dashed border-gray-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Jasa & Service
                    </h3>
                    <div class="space-y-2">
                        <template x-for="item in cartServices" :key="item.id">
                            <div class="bg-blue-50 p-2.5 rounded-lg border border-blue-100 flex justify-between items-center relative overflow-hidden group hover:border-blue-300 transition">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                                <div class="flex-1 pl-3">
                                    <div class="flex justify-between items-start">
                                        <div class="font-bold text-sm text-gray-800 line-clamp-1" x-text="item.name"></div>
                                        <button @click="removeFromCart(item.id)" class="text-gray-300 hover:text-red-500 px-2 font-bold">×</button>
                                    </div>
                                    <div class="flex justify-between items-center mt-0.5">
                                        <div class="text-[10px] text-gray-500" x-text="item.is_custom ? 'Jasa Manual' : 'Paket Service'"></div>
                                        <div class="font-bold text-sm text-blue-700" x-text="formatRupiah(item.price * item.qty)"></div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center ml-2 space-y-1">
                                    <button @click="updateQty(item.id, 1)" class="w-5 h-4 bg-white border border-gray-200 hover:bg-green-50 text-green-700 rounded text-[9px]">▲</button>
                                    <span class="font-bold text-xs text-gray-800" x-text="item.qty"></span>
                                    <button @click="updateQty(item.id, -1)" class="w-5 h-4 bg-white border border-gray-200 hover:bg-red-50 text-red-700 rounded text-[9px]">▼</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="cart.length === 0">
                    <div class="h-40 flex flex-col items-center justify-center text-gray-400 opacity-60">
                        <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-xs">Keranjang Kosong</p>
                    </div>
                </template>
            </div>

            <div class="bg-white border-t border-gray-200 shrink-0 p-4">

                <div class="flex justify-between text-xs text-gray-500 mb-2 px-1">
                    <span>Part: <span class="font-bold text-gray-700" x-text="formatRupiah(totalPartsPrice)"></span></span>
                    <span>Jasa: <span class="font-bold text-gray-700" x-text="formatRupiah(totalServicesPrice)"></span></span>
                </div>

                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600 text-sm font-bold">Total Tagihan</span>
                    <span class="text-2xl font-extrabold text-brand-900" x-text="formatRupiah(cartTotal)"></span>
                </div>

                <div class="relative mb-3">
                    <span class="absolute left-3 top-2.5 text-gray-400 font-bold text-sm">Rp</span>
                    <input type="number"
                           x-model="cashReceived"
                           @keydown.enter.prevent="processCheckout()"
                           placeholder="Nominal Bayar"
                           class="w-full pl-9 pr-3 py-2.5 rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 font-mono text-lg shadow-inner bg-gray-50">
                </div>

                <button type="button"
                        @click="processCheckout()"
                        :disabled="cart.length===0 || isLoading"
                        class="w-full py-3 bg-brand-900 text-white font-bold rounded-lg hover:bg-black transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center">

                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span x-text="isLoading ? 'MEMPROSES...' : 'PROSES TRANSAKSI'"></span>
                </button>
            </div>
        </div>

        <div x-show="showCustomServiceModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden" @click.away="showCustomServiceModal = false">
                <div class="bg-blue-600 p-3 text-white font-bold flex justify-between items-center">
                    <h3 class="text-sm">Input Jasa Manual</h3>
                    <button @click="showCustomServiceModal = false" class="hover:text-blue-200">✕</button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Nama Jasa</label>
                        <input type="text" x-model="customService.name" class="w-full rounded-md border-gray-300 focus:ring-blue-500 text-sm" placeholder="Contoh: Las Knalpot" autofocus>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Biaya (Rp)</label>
                        <input type="number" x-model="customService.price" class="w-full rounded-md border-gray-300 focus:ring-blue-500 font-mono text-sm" placeholder="0">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button @click="addCustomService()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-bold text-sm shadow">Tambahkan</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showCustModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showCustModal = false">
                <div class="bg-gray-50 p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800" x-text="addCustMode ? 'Tambah Pelanggan Baru' : 'Cari Pelanggan'"></h3>
                    <button @click="closeCustomerModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div x-show="!addCustMode" class="p-4">
                    <input type="text" x-model="custQuery" @input.debounce.300ms="searchCustomers()" placeholder="Ketik Nama atau No HP..."
                           class="w-full border-gray-300 rounded-lg p-2.5 mb-4 focus:ring-brand-500 shadow-sm" autofocus>
                    <ul class="max-h-60 overflow-y-auto divide-y divide-gray-100 custom-scrollbar border border-gray-100 rounded-lg">
                        <template x-for="c in custResults" :key="c.id">
                            <li @click="selectCustomer(c)" class="p-3 hover:bg-brand-50 cursor-pointer flex justify-between items-center transition">
                                <div><div class="font-bold text-gray-800 text-sm" x-text="c.name"></div><div class="text-xs text-gray-500" x-text="c.phone"></div></div>
                                <span class="text-brand-600 text-[10px] font-bold bg-brand-100 px-2 py-1 rounded">PILIH</span>
                            </li>
                        </template>
                        <template x-if="custResults.length === 0 && custQuery.length > 0">
                            <li class="p-6 text-center"><p class="text-gray-500 text-sm mb-3">Pelanggan tidak ditemukan.</p><button @click="addCustMode = true; newCust.name = custQuery" class="px-4 py-2 bg-brand-900 text-white text-sm font-bold rounded-lg hover:bg-black w-full">+ Daftarkan Baru</button></li>
                        </template>
                    </ul>
                </div>
                <div x-show="addCustMode" class="p-6 space-y-4">
                    <div><label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap</label><input type="text" x-model="newCust.name" class="w-full rounded-md border-gray-300 text-sm"></div>
                    <div><label class="block text-xs font-bold text-gray-600 mb-1">No. HP / WA</label><input type="number" x-model="newCust.phone" class="w-full rounded-md border-gray-300 text-sm"></div>
                    <div><label class="block text-xs font-bold text-gray-600 mb-1">Alamat</label><textarea x-model="newCust.address" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
                    <div class="flex justify-between pt-2"><button @click="addCustMode = false" class="text-gray-500 text-sm underline">Kembali</button><button @click="saveNewCustomer()" class="px-6 py-2 bg-green-600 text-white rounded-md font-bold text-sm shadow">Simpan & Pilih</button></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                searchQuery: '', activeTab: 'goods', products: [], cart: [], cashReceived: '',
                showCustomServiceModal: false, customService: { name: '', price: '' },
                customer: null, showCustModal: false, custQuery: '', custResults: [], addCustMode: false, newCust: { name: '', phone: '', address: '' },
                isLoading: false,

                init() { this.fetchProducts(); },
                fetchProducts() { fetch(`/pos/search?query=${this.searchQuery}`).then(r=>r.json()).then(d=>this.products=d); },

                // Computed Properties
                get filteredProducts() {
                    return this.products.filter(p => {
                        if (this.activeTab === 'goods') return p.type === 'goods' || !p.type;
                        if (this.activeTab === 'service') return p.type === 'service';
                    });
                },
                get cartParts() { return this.cart.filter(i => i.type === 'goods' || (!i.type && !i.is_custom)); },
                get cartServices() { return this.cart.filter(i => i.type === 'service' || i.is_custom); },
                get totalPartsPrice() { return this.cartParts.reduce((s,i) => s + (i.price*i.qty), 0); },
                get totalServicesPrice() { return this.cartServices.reduce((s,i) => s + (i.price*i.qty), 0); },
                get cartTotal() { return this.cart.reduce((s,i)=>s+(i.price*i.qty),0); },
                cartTotalQty() { return this.cart.reduce((s,i)=>s+i.qty,0); },
                formatRupiah(n) { return new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n); },

                // Cart Logic
                addToCart(p) {
                    let item = this.cart.find(i => i.id === p.id && !i.is_custom);
                    if(item) {
                        if(p.type === 'goods' && item.qty >= p.stock_quantity) {
                            Swal.fire({ icon: 'error', title: 'Stok Habis!', text: 'Stok produk ini sudah maksimal di keranjang.', confirmButtonColor: '#fb923c' });
                        } else item.qty++;
                    }
                    else { let max = p.type === 'goods' ? p.stock_quantity : 9999; this.cart.push({...p, price: p.sell_price, qty: 1, max: max, is_custom: false}); }
                },
                addCustomService() {
                    if(!this.customService.name || !this.customService.price) {
                        return Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon isi nama jasa dan biaya.', confirmButtonColor: '#fb923c' });
                    }
                    this.cart.push({ id: 'custom_'+Date.now(), name: this.customService.name, price: parseInt(this.customService.price), qty: 1, max: 9999, type: 'service', is_custom: true });
                    this.showCustomServiceModal = false; this.customService = {name:'', price:''};
                },
                removeFromCart(id) {
                    let index = this.cart.findIndex(i => i.id === id);
                    if(index !== -1) this.cart.splice(index, 1);
                },
                updateQty(id, v) {
                    let index = this.cart.findIndex(i => i.id === id);
                    if(index !== -1) {
                        let item = this.cart[index];
                        let n = item.qty + v;
                        if(n > 0 && n <= item.max) item.qty = n;
                        else if(n <= 0) this.cart.splice(index, 1);
                    }
                },

                // Customer Logic
                openCustomerModal() { this.showCustModal = true; this.addCustMode = false; this.custQuery = ''; this.custResults = []; },
                closeCustomerModal() { this.showCustModal = false; },
                searchCustomers() { if (this.custQuery.length > 1) fetch(`/customers/search?query=${this.custQuery}`).then(r => r.json()).then(d => this.custResults = d); },
                selectCustomer(c) { this.customer = c; this.closeCustomerModal(); },
                saveNewCustomer() {
                    if(!this.newCust.name || !this.newCust.phone) return Swal.fire({ icon: 'warning', title: 'Data Kurang', text: 'Nama dan No HP Wajib diisi!', confirmButtonColor: '#fb923c' });
                    fetch('/customers/quick-store', {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify(this.newCust)
                    }).then(r => r.json()).then(d => {
                        if(d.status === 'success') { this.selectCustomer(d.customer); this.newCust = { name: '', phone: '', address: '' }; }
                        else Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan pelanggan.', confirmButtonColor: '#ef4444' });
                    });
                },

                // ===============================================
                // CHECKOUT PROCESS (DENGAN SWEETALERT 2)
                // ===============================================
                async processCheckout() {
                    // 1. Cek Uang Kurang
                    if(Number(this.cashReceived) < this.cartTotal) {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'Uang Kurang!',
                            text: 'Nominal pembayaran belum cukup. Harap cek kembali.',
                            confirmButtonText: 'Oke, Siap',
                            confirmButtonColor: '#fb923c', // Soft Orange
                            iconColor: '#ea580c', // Orange Tua
                        });
                    }

                    this.isLoading = true;

                    try {
                        const response = await fetch('/pos/checkout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                cash_received: this.cashReceived,
                                customer_id: this.customer?.id
                            })
                        });

                        if (!response.ok) throw new Error(`Server Error (${response.status})`);

                        const data = await response.json();

                        if(data.status === 'success') {
                            // Buka Nota
                            window.open(`/transaction/${data.invoice}/receipt`, '_blank', 'width=400,height=600');

                            // Reset POS
                            this.cart = [];
                            this.cashReceived = '';
                            this.customer = null;
                            this.fetchProducts();

                        } else {
                            Swal.fire({ icon: 'error', title: 'Transaksi Gagal', text: data.message, confirmButtonColor: '#ef4444' });
                        }

                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Cek koneksi atau database. Lihat Console (F12) untuk detail.',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
