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
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
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
                                <h4 class="font-bold text-gray-800 text-xs leading-tight line-clamp-2" x-text="product.name"></h4>
                                <div class="text-brand-700 font-bold text-sm text-right" x-text="formatRupiah(product.sell_price)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 bg-white flex flex-col shadow-2xl z-20 h-full border-l border-gray-200">
            <div class="px-5 py-3 bg-brand-900 text-white flex justify-between items-center shadow-md shrink-0">
                <h3 class="font-bold text-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Keranjang
                </h3>
                <button @click="resetCart()" class="text-xs font-bold bg-red-600 hover:bg-red-700 px-3 py-1 rounded" x-show="cart.length > 0">KOSONGKAN</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-200 shadow-sm group">
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50 h-10">
                            <button @click="updateQty(index, -1)" class="w-10 h-full text-red-600 font-bold">-</button>
                            <input type="text" readonly class="w-12 h-full text-center font-bold bg-transparent border-0 p-0" x-model="item.qty">
                            <button @click="updateQty(index, 1)" class="w-10 h-full text-green-600 font-bold">+</button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-gray-800 text-base" x-text="item.name"></h4>
                                <div class="text-right">
                                    <div class="font-black text-brand-800 text-lg" x-text="formatRupiah(item.price * item.qty)"></div>
                                    <div class="text-xs text-gray-400" x-text="'@ ' + formatRupiah(item.price)"></div>
                                </div>
                            </div>
                        </div>
                        <button @click="removeFromCart(index)" class="text-gray-400 hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50">
                    <p class="text-lg font-bold">Keranjang Kosong</p>
                </div>
            </div>

            <div class="bg-white p-6 border-t border-gray-300 shadow-[0_-5px_30px_rgba(0,0,0,0.1)]">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Pelanggan</label>
                        <button @click="openCustomerModal()" class="w-full text-left rounded-xl border border-gray-300 bg-gray-50 py-3 px-3 font-bold text-gray-700 flex justify-between">
                            <span x-text="selectedCustomer ? selectedCustomer.name : 'Umum'"></span>
                            <span class="text-blue-600 text-[10px]">UBAH</span>
                        </button>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Diskon</label>
                            <button @click="toggleDiscountMode()" class="text-[10px] font-bold text-blue-600" x-text="manualDiscountMode ? 'Ganti Kode' : 'Manual'"></button>
                        </div>
                        <select x-show="!manualDiscountMode" x-model="selectedPromoId" @change="calculateTotal()" class="w-full rounded-xl border-gray-300 bg-gray-50 font-bold text-brand-700">
                            <option value="">- Tanpa Diskon -</option>
                            @foreach($promotions as $promo)
                                <option value="{{ $promo->id }}">{{ $promo->name }}</option>
                            @endforeach
                        </select>
                        <input x-show="manualDiscountMode" type="number" x-model.number="manualValue" @input="calculateTotal()" class="w-full rounded-xl border-brand-300 font-bold text-red-600">
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-dashed pt-4 mb-4">
                    <span class="text-xs text-gray-500 font-bold uppercase">Total Tagihan</span>
                    <span class="font-black text-4xl text-brand-900" x-text="formatRupiah(grandTotal)"></span>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/2 relative">
                        <label class="absolute -top-2.5 left-3 bg-white px-1 text-[10px] font-bold text-gray-500">PEMBAYARAN</label>
                        <input type="number" x-model.number="paidAmount" class="w-full pl-4 pr-4 py-4 rounded-xl border-2 border-gray-300 text-2xl font-black bg-gray-50" placeholder="Rp">
                    </div>
                    <button @click="submitTransaction()" :disabled="cart.length === 0 || paidAmount < grandTotal || isLoading"
                            class="flex-1 rounded-xl font-black text-xl text-white shadow-xl transition-all"
                            :class="cart.length === 0 || paidAmount < grandTotal ? 'bg-gray-300' : 'bg-brand-900 hover:bg-black'">
                        <span x-show="!isLoading">BAYAR</span>
                        <span x-show="isLoading" class="animate-spin">⌛</span>
                    </button>
                </div>

                <div class="text-right mt-2 h-6">
                    <span class="text-sm font-bold text-green-600" x-show="paidAmount >= grandTotal && grandTotal > 0">KEMBALI: <span x-text="formatRupiah(paidAmount - grandTotal)"></span></span>
                </div>
            </div>
        </div>

        <div x-show="showCustomerModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCustomerModal = false"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Cari Pelanggan</h3><button @click="showCustomerModal = false">✕</button></div>
                    <input type="text" x-model="customerSearch" x-ref="cusSearchInput" placeholder="Ketik Nama..." class="w-full rounded-xl border-gray-300 font-bold mb-4">
                    <div class="h-64 overflow-y-auto border rounded-xl bg-gray-50">
                        <template x-for="cus in filteredCustomers" :key="cus.id">
                            <div @click="selectCustomer(cus)" class="p-3 border-b hover:bg-brand-100 cursor-pointer flex justify-between">
                                <div><div class="font-bold text-sm" x-text="cus.name"></div><div class="text-xs text-gray-500" x-text="cus.phone || '-'"></div></div>
                                <span class="text-xs font-bold text-brand-600 uppercase">Pilih</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="bg-gray-100 p-4 text-right"><button @click="selectCustomer(null)" class="text-xs font-bold text-gray-600">RESET KE UMUM</button></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', (products, promotions, customers) => ({
                products, promotions, customers,
                search: '', filterType: 'goods', cart: [],
                showCustomerModal: false, customerSearch: '', selectedCustomer: null, customerId: '',
                manualDiscountMode: false, manualType: 'fixed', manualValue: '', selectedPromoId: '',
                paymentMethod: 'cash', paidAmount: '', subtotal: 0, discount: 0, grandTotal: 0, isLoading: false,

                get filteredProducts() {
                    let items = this.products.filter(p => p.type === this.filterType);
                    if (this.search) items = items.filter(p => p.name.toLowerCase().includes(this.search.toLowerCase()) || p.product_code.toLowerCase().includes(this.search.toLowerCase()));
                    return items;
                },
                get filteredCustomers() {
                    if (!this.customerSearch) return this.customers;
                    return this.customers.filter(c => c.name.toLowerCase().includes(this.customerSearch.toLowerCase()) || (c.phone && c.phone.includes(this.customerSearch)));
                },
                async openManualItemModal() {
                    const { value: formValues } = await Swal.fire({
                        title: 'Isi Manual',
                        html: '<input id="swal-input1" class="swal2-input" placeholder="Nama Barang/Jasa"><input id="swal-input2" type="number" class="swal2-input" placeholder="Harga">',
                        showCancelButton: true, confirmButtonText: 'Tambah', confirmButtonColor: '#111827',
                        preConfirm: () => [document.getElementById('swal-input1').value, document.getElementById('swal-input2').value]
                    });
                    if (formValues && formValues[0] && formValues[1]) {
                        this.cart.push({ id: 'MAN_' + Date.now(), name: formValues[0], price: parseInt(formValues[1]), qty: 1, is_custom: true });
                        this.calculateTotal();
                    }
                },
                openCustomerModal() { this.showCustomerModal = true; this.$nextTick(() => this.$refs.cusSearchInput.focus()); },
                selectCustomer(c) { this.selectedCustomer = c; this.customerId = c ? c.id : ''; this.showCustomerModal = false; },
                toggleDiscountMode() { this.manualDiscountMode = !this.manualDiscountMode; this.calculateTotal(); },
                addToCart(p) {
                    if (p.type === 'goods' && p.stock_quantity <= 0) return Swal.fire({ icon: 'error', title: 'Stok Habis', toast: true, position: 'top-end', timer: 1500 });
                    let item = this.cart.find(i => i.id === p.id);
                    if (item) item.qty++;
                    else this.cart.push({ id: p.id, name: p.name, price: p.sell_price, type: p.type, max_stock: p.stock_quantity, qty: 1, is_custom: false });
                    this.calculateTotal();
                },
                updateQty(idx, amt) {
                    let item = this.cart[idx];
                    item.qty += amt;
                    if (item.qty <= 0) this.cart.splice(idx, 1);
                    this.calculateTotal();
                },
                removeFromCart(idx) { this.cart.splice(idx, 1); this.calculateTotal(); },
                resetCart() { if(confirm('Kosongkan keranjang?')) { this.cart = []; this.calculateTotal(); } },
                calculateTotal() {
                    this.subtotal = this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
                    this.discount = 0;
                    if (this.manualDiscountMode) this.discount = parseFloat(this.manualValue) || 0;
                    else if (this.selectedPromoId) {
                        let p = this.promotions.find(x => x.id == this.selectedPromoId);
                        if (p) this.discount = p.discount_type === 'fixed' ? parseFloat(p.value) : this.subtotal * (parseFloat(p.value) / 100);
                    }
                    this.grandTotal = Math.max(0, this.subtotal - this.discount);
                },
                formatRupiah(n) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n); },

                async submitTransaction() {
                    this.isLoading = true;
                    try {
                        let res = await fetch("{{ route('pos.store') }}", {
                            method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                            body: JSON.stringify({ cart: this.cart, customer_id: this.customerId, paid_amount: this.paidAmount, payment_method: this.paymentMethod, promotion_id: this.selectedPromoId, manual_discount: this.manualDiscountMode ? this.discount : 0 })
                        });
                        let data = await res.json();
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `<h2 class="text-4xl font-black text-green-600">${this.formatRupiah(data.change)}</h2><p>Kembalian</p>`,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: '🖨️ CETAK STRUK',
                                cancelButtonText: 'Selesai',
                                confirmButtonColor: '#F97316'
                            }).then((r) => {
                                if (r.isConfirmed) {
                                    // BUKA POP-UP BARU UNTUK NGE-PRINT LEWAT CHROME -> RAWBT PRINT SERVICE
                                    window.open(`/pos/print/${data.invoice_code}`, '_blank');
                                    setTimeout(() => window.location.reload(), 2000);
                                } else window.location.reload();
                            });
                        } else throw new Error(data.message);
                    } catch (e) { Swal.fire('Gagal', e.message, 'error'); } finally { this.isLoading = false; }
                }
            }));
        });
    </script>
</x-app-layout>
