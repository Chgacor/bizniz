<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
                <span>🖥️</span> {{ __('Kasir Bengkel (POS)') }}
            </h2>
            <div class="text-sm font-mono bg-white px-3 py-1 rounded border border-gray-200 text-gray-500">
                {{ date('d M Y') }}
            </div>
        </div>
    </x-slot>

    <div class="h-[calc(100vh-140px)] bg-gray-100 flex flex-col md:flex-row overflow-hidden"
         x-data="posSystem({{ json_encode($products) }}, {{ json_encode($promotions) }}, {{ json_encode($customers) }})">

        <div class="w-full md:w-1/2 flex flex-col border-r border-gray-200 bg-gray-50">

            <div class="p-3 bg-white border-b border-gray-200 space-y-2 shadow-sm z-10">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="search" placeholder="Cari barang / scan barcode..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition text-sm font-bold text-gray-700"
                           autofocus>
                </div>

                <div class="flex gap-2">
                    <div class="flex flex-1 p-1 bg-gray-100 rounded-lg">
                        <button @click="filterType = 'goods'"
                                :class="filterType === 'goods' ? 'bg-white text-orange-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 py-2 text-sm font-bold rounded-md transition flex justify-center items-center gap-2">
                            <span>📦</span> Sparepart
                        </button>
                        <button @click="filterType = 'service'"
                                :class="filterType === 'service' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 py-2 text-sm font-bold rounded-md transition flex justify-center items-center gap-2">
                            <span>🔧</span> Jasa
                        </button>
                    </div>
                    <button @click="openManualItemModal()"
                            class="px-3 bg-gray-800 text-white rounded-lg font-bold text-xs hover:bg-black shadow flex flex-col items-center justify-center leading-tight transition">
                        <span>+ Isi</span>
                        <span>Manual</span>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-3 custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                             class="bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-brand-500 hover:shadow-md transition group flex flex-col relative overflow-hidden h-[160px]">

                            <div class="absolute top-2 right-2 z-10">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border shadow-sm"
                                      :class="product.type === 'service' ? 'bg-blue-50 text-blue-600 border-blue-100' : (product.stock_quantity > 0 ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100')">
                                    <span x-text="product.type === 'service' ? 'JASA' : product.stock_quantity"></span>
                                </span>
                            </div>

                            <div class="h-20 w-full bg-gray-50 flex items-center justify-center text-3xl group-hover:bg-brand-50 transition duration-300">
                                <template x-if="product.image_path">
                                    <img :src="'/storage/' + product.image_path" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!product.image_path">
                                    <span x-text="product.type === 'service' ? '🔧' : '📦'" class="opacity-50 group-hover:opacity-100 transition"></span>
                                </template>
                            </div>

                            <div class="p-2 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-xs leading-tight line-clamp-2" x-text="product.name"></h4>
                                </div>
                                <div class="text-brand-700 font-bold text-sm text-right" x-text="formatRupiah(product.sell_price)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 bg-white flex flex-col shadow-2xl z-20 h-full border-l border-gray-200">

            <div class="px-5 py-3 bg-brand-900 text-white flex justify-between items-center shadow-md shrink-0">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <h3 class="font-bold text-lg">Keranjang Belanja</h3>
                </div>
                <button @click="resetCart()" class="text-xs font-bold bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white transition" x-show="cart.length > 0">
                    KOSONGKAN
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-brand-400 transition group">

                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50 h-10">
                            <button @click="updateQty(index, -1)" class="w-10 h-full flex items-center justify-center text-red-600 hover:bg-red-100 transition font-bold text-lg">-</button>
                            <input type="text" readonly class="w-12 h-full text-center border-0 bg-transparent font-bold text-gray-800 p-0" x-model="item.qty">
                            <button @click="updateQty(index, 1)" class="w-10 h-full flex items-center justify-center text-green-600 hover:bg-green-100 transition font-bold text-lg">+</button>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-base leading-tight" x-text="item.name"></h4>
                                    <span x-show="item.is_custom" class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded mt-1 inline-block">MANUAL ITEM</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-brand-800 text-lg" x-text="formatRupiah(item.price * item.qty)"></div>
                                    <div class="text-xs text-gray-400" x-text="'@ ' + formatRupiah(item.price)"></div>
                                </div>
                            </div>
                        </div>

                        <button @click="removeFromCart(index)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50">
                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-lg font-bold">Keranjang Masih Kosong</p>
                </div>
            </div>

            <div class="bg-white p-6 border-t border-gray-300 shadow-[0_-5px_30px_rgba(0,0,0,0.1)] z-30">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Pelanggan</label>
                        <button @click="openCustomerModal()"
                                class="w-full text-left text-sm rounded-xl border border-gray-300 bg-gray-50 py-3 px-3 flex items-center justify-between hover:border-brand-500 transition">
                            <span class="truncate font-bold text-gray-700" x-text="selectedCustomer ? selectedCustomer.name : 'Umum'"></span>
                            <span class="text-[10px] font-bold text-blue-600 uppercase">Ubah</span>
                        </button>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Diskon</label>
                            <button @click="toggleDiscountMode()" class="text-[10px] font-bold text-blue-600 hover:underline">
                                <span x-text="manualDiscountMode ? 'Ganti Kode' : 'Manual'"></span>
                            </button>
                        </div>

                        <div x-show="!manualDiscountMode">
                            <select x-model="selectedPromoId" @change="calculateTotal()"
                                    class="w-full text-sm rounded-xl border-gray-300 bg-gray-50 py-3 px-3 font-bold text-brand-700">
                                <option value="">- Tidak Ada -</option>
                                @foreach($promotions as $promo)
                                    <option value="{{ $promo->id }}" data-type="{{ $promo->discount_type }}" data-value="{{ $promo->value }}">
                                        {{ $promo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="manualDiscountMode" style="display: none;" class="flex gap-2">
                            <input type="number" x-model.number="manualValue" @input="calculateTotal()" placeholder="Nominal" class="w-full text-sm rounded-xl border-brand-300 font-bold text-red-600 py-3">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-4 mb-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-500 font-bold">Total Tagihan</span>
                        <div class="text-xs text-red-500" x-show="discount > 0">Hemat: <span x-text="formatRupiah(discount)"></span></div>
                    </div>
                    <span class="font-black text-4xl text-brand-900" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/2 relative">
                        <label class="absolute -top-2.5 left-3 bg-white px-1 text-[10px] font-bold text-gray-500">PEMBAYARAN</label>
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black text-lg">Rp</span>
                        <input type="number" x-model.number="paidAmount"
                               class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-300 focus:border-brand-500 focus:ring-0 text-2xl font-black font-mono shadow-inner bg-gray-50"
                               placeholder="Input Angka">
                    </div>

                    <button @click="submitTransaction()"
                            :disabled="cart.length === 0 || paidAmount < grandTotal || isLoading"
                            class="flex-1 rounded-xl font-black text-xl text-white shadow-xl transition-all transform flex items-center justify-center gap-2 active:scale-95"
                            :class="cart.length === 0 || paidAmount < grandTotal ? 'bg-gray-300 cursor-not-allowed' : 'bg-brand-900 hover:bg-black hover:-translate-y-1'">
                        <span x-show="!isLoading">BAYAR</span>
                        <span x-show="isLoading" class="animate-spin">⌛</span>
                    </button>
                </div>

                <div class="text-right h-6 mt-2">
                    <span class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100" x-show="paidAmount >= grandTotal && grandTotal > 0">
                        KEMBALI: <span x-text="formatRupiah(paidAmount - grandTotal)"></span>
                    </span>
                    <span class="text-xs font-bold text-red-500" x-show="paidAmount < grandTotal && paidAmount > 0">
                        KURANG: <span x-text="formatRupiah(grandTotal - paidAmount)"></span>
                    </span>
                </div>

            </div>
        </div>

        <div x-show="showCustomerModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCustomerModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCustomerModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showCustomerModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Cari Pelanggan</h3>
                            <button @click="showCustomerModal = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                        </div>
                        <div class="mb-4">
                            <input type="text" x-model="customerSearch" x-ref="cusSearchInput" placeholder="Ketik Nama / No HP..." class="w-full rounded-xl border-gray-300 py-2.5 px-4 font-bold shadow-sm">
                        </div>
                        <div class="h-64 overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 custom-scrollbar">
                            <template x-for="cus in filteredCustomers" :key="cus.id">
                                <div @click="selectCustomer(cus)" class="p-3 hover:bg-brand-100 cursor-pointer border-b border-gray-200 flex justify-between items-center group">
                                    <div>
                                        <div class="font-bold text-sm" x-text="cus.name"></div>
                                        <div class="text-xs text-gray-500" x-text="cus.phone || '-'"></div>
                                    </div>
                                    <div class="text-xs font-bold text-brand-600 opacity-0 group-hover:opacity-100">PILIH</div>
                                </div>
                            </template>
                            <div x-show="filteredCustomers.length === 0" class="p-8 text-center text-gray-400 text-xs italic">Tidak ditemukan.</div>
                        </div>
                    </div>
                    <div class="bg-gray-100 px-6 py-3 flex justify-end">
                        <button type="button" @click="selectCustomer(null)" class="text-xs font-bold text-gray-600 hover:text-brand-700">RESET KE UMUM</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', (products, promotions, customers) => ({
                products: products, promotions: promotions, customers: customers,
                search: '', filterType: 'goods', cart: [],
                showCustomerModal: false, customerSearch: '', selectedCustomer: null, customerId: '',
                manualDiscountMode: false, manualType: 'fixed', manualValue: '', selectedPromoId: '',
                // FORCE DEFAULT CASH agar backend validasi aman & kembalian muncul
                paymentMethod: 'cash',
                paidAmount: '', subtotal: 0, discount: 0, grandTotal: 0, isLoading: false,

                get filteredProducts() {
                    let items = this.products.filter(p => p.type === this.filterType);
                    if (this.search !== '') {
                        items = items.filter(p => p.name.toLowerCase().includes(this.search.toLowerCase()) || p.product_code.toLowerCase().includes(this.search.toLowerCase()));
                    }
                    return items;
                },
                get filteredCustomers() {
                    if (this.customerSearch === '') return this.customers;
                    return this.customers.filter(c => c.name.toLowerCase().includes(this.customerSearch.toLowerCase()) || (c.phone && c.phone.includes(this.customerSearch)));
                },
                async openManualItemModal() {
                    const { value: formValues } = await Swal.fire({
                        title: 'Isi Manual',
                        html: '<input id="swal-input1" class="swal2-input" placeholder="Nama Barang/Jasa"><input id="swal-input2" type="number" class="swal2-input" placeholder="Harga">',
                        focusConfirm: false, showCancelButton: true, confirmButtonText: 'Tambah', confirmButtonColor: '#111827',
                        preConfirm: () => [document.getElementById('swal-input1').value, document.getElementById('swal-input2').value]
                    });
                    if (formValues && formValues[0] && formValues[1]) this.addManualItem(formValues[0], parseInt(formValues[1]));
                },
                addManualItem(name, price) {
                    this.cart.push({ id: 'MAN_' + Date.now(), name: name, price: price, qty: 1, is_custom: true });
                    this.calculateTotal();
                },
                openCustomerModal() { this.showCustomerModal = true; this.customerSearch = ''; this.$nextTick(() => this.$refs.cusSearchInput.focus()); },
                selectCustomer(c) { this.selectedCustomer = c; this.customerId = c ? c.id : ''; this.showCustomerModal = false; },
                toggleDiscountMode() { this.manualDiscountMode = !this.manualDiscountMode; this.selectedPromoId = ''; this.manualValue = ''; this.calculateTotal(); },
                addToCart(p) {
                    if (p.type === 'goods' && p.stock_quantity <= 0) return Swal.fire({ icon: 'error', title: 'Stok Habis', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                    let item = this.cart.find(i => i.id === p.id);
                    if (item) {
                        if (p.type === 'goods' && item.qty >= p.stock_quantity) return Swal.fire({ icon: 'warning', title: 'Stok Limit', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                        item.qty++;
                    } else {
                        this.cart.push({ id: p.id, name: p.name, price: p.sell_price, type: p.type, max_stock: p.stock_quantity, qty: 1, is_custom: false });
                    }
                    this.calculateTotal();
                },
                updateQty(idx, amt) {
                    let item = this.cart[idx];
                    let newQty = item.qty + amt;
                    if (newQty <= 0) this.cart.splice(idx, 1);
                    else {
                        if (!item.is_custom && item.type === 'goods' && newQty > item.max_stock) return Swal.fire({ icon: 'warning', title: 'Stok Limit', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                        item.qty = newQty;
                    }
                    this.calculateTotal();
                },
                removeFromCart(idx) { this.cart.splice(idx, 1); this.calculateTotal(); },
                resetCart() { if(confirm('Hapus semua?')) { this.cart = []; this.paidAmount = ''; this.calculateTotal(); } },
                calculateTotal() {
                    this.subtotal = this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
                    this.discount = 0;
                    if (this.manualDiscountMode) {
                        let val = parseFloat(this.manualValue) || 0;
                        this.discount = (this.manualType === 'fixed') ? val : this.subtotal * (val / 100);
                    } else if (this.selectedPromoId) {
                        let p = this.promotions.find(x => x.id == this.selectedPromoId);
                        if (p) this.discount = (p.discount_type === 'fixed') ? parseFloat(p.value) : this.subtotal * (parseFloat(p.value) / 100);
                    }
                    this.grandTotal = Math.max(0, this.subtotal - this.discount);
                },
                formatRupiah(n) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n); },
                async submitTransaction() {
                    this.isLoading = true;
                    // Selalu kirim nominal yang diinput user
                    try {
                        let res = await fetch("{{ route('pos.store') }}", {
                            method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                            body: JSON.stringify({ cart: this.cart, customer_id: this.customerId, paid_amount: this.paidAmount, payment_method: this.paymentMethod, promotion_id: !this.manualDiscountMode ? this.selectedPromoId : null, manual_discount: this.manualDiscountMode ? this.discount : 0 })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Sukses!', html: `<h2 class="text-4xl font-black text-green-600">${this.formatRupiah(data.change)}</h2><p>Kembalian</p>`, icon: 'success', showCancelButton: true, confirmButtonText: 'Cetak Struk', cancelButtonText: 'Tutup'
                            }).then((r) => {
                                if (r.isConfirmed) { window.open(`/pos/print/${data.invoice_code}`, '_blank', 'width=400,height=600'); setTimeout(() => window.location.reload(), 1000); }
                                else window.location.reload();
                            });
                        } else throw new Error(data.message);
                    } catch (e) { Swal.fire('Gagal', e.message, 'error'); } finally { this.isLoading = false; }
                }
            }));
        });
    </script>
</x-app-layout>
