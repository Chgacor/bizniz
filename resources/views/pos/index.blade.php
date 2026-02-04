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

        <div class="w-full md:w-8/12 lg:w-3/4 flex flex-col border-r border-gray-200 bg-gray-50">

            <div class="p-4 bg-white border-b border-gray-200 space-y-3 shadow-sm z-10">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="search" placeholder="Cari sparepart, jasa, atau scan barcode..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition text-sm font-bold text-gray-700"
                           autofocus>
                </div>

                <div class="flex p-1 bg-gray-100 rounded-lg">
                    <button @click="filterType = 'goods'"
                            :class="filterType === 'goods' ? 'bg-white text-orange-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-2.5 text-sm font-bold rounded-md transition flex justify-center items-center gap-2">
                        <span>📦</span> Sparepart (Barang)
                    </button>
                    <button @click="filterType = 'service'"
                            :class="filterType === 'service' ? 'bg-white text-blue-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-2.5 text-sm font-bold rounded-md transition flex justify-center items-center gap-2">
                        <span>🔧</span> Jasa Service
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                             class="bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-brand-500 hover:shadow-md transition group flex flex-col relative overflow-hidden h-[180px]">

                            <div class="absolute top-2 right-2 z-10">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border shadow-sm"
                                      :class="product.type === 'service' ? 'bg-blue-50 text-blue-600 border-blue-100' : (product.stock_quantity > 0 ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100')">
                                    <span x-text="product.type === 'service' ? 'JASA' : product.stock_quantity"></span>
                                </span>
                            </div>

                            <div class="h-24 w-full bg-gray-50 flex items-center justify-center text-3xl group-hover:bg-brand-50 transition duration-300">
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
                                    <p class="text-[10px] text-gray-400 font-mono mt-0.5" x-text="product.product_code"></p>
                                </div>
                                <div class="text-brand-700 font-bold text-sm text-right" x-text="formatRupiah(product.sell_price)"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 pb-20">
                    <svg class="w-12 h-12 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="text-sm">Item tidak ditemukan di kategori ini</p>
                </div>
            </div>
        </div>

        <div class="w-full md:w-4/12 lg:w-1/4 bg-white flex flex-col shadow-xl z-20 h-full border-l border-gray-200">

            <div class="px-4 py-3 bg-gray-900 text-white flex justify-between items-center shadow-md shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <h3 class="font-bold text-sm">Keranjang</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-gray-700 px-2 py-0.5 rounded text-xs" x-text="cart.length + ' Item'"></span>
                    <button @click="resetCart()" class="text-xs text-red-300 hover:text-red-100 transition" x-show="cart.length > 0">Reset</button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-white">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex gap-2 p-2 rounded-lg border border-gray-100 hover:border-gray-200 transition group">
                        <div class="flex flex-col items-center justify-between bg-gray-50 rounded px-1 py-1 w-8">
                            <button @click="updateQty(index, 1)" class="text-green-600 hover:bg-green-100 rounded w-full text-center text-xs font-bold">+</button>
                            <span class="text-xs font-bold text-gray-800 my-1" x-text="item.qty"></span>
                            <button @click="updateQty(index, -1)" class="text-red-600 hover:bg-red-100 rounded w-full text-center text-xs font-bold">-</button>
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div class="flex justify-between items-start gap-1">
                                <h4 class="font-bold text-gray-700 text-xs leading-tight line-clamp-2" x-text="item.name"></h4>
                                <button @click="removeFromCart(index)" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">×</button>
                            </div>
                            <div class="flex justify-between items-end mt-1">
                                <span class="text-[10px] text-gray-400" x-text="formatRupiah(item.price)"></span>
                                <span class="font-bold text-brand-700 text-sm" x-text="formatRupiah(item.price * item.qty)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="h-40 flex flex-col items-center justify-center text-gray-300 border-2 border-dashed border-gray-100 rounded-lg m-2">
                    <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-xs">Keranjang Kosong</p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">

                <div class="mb-2">
                    <button @click="openCustomerModal()"
                            class="w-full text-xs rounded border border-gray-300 bg-white py-2 px-2 flex items-center justify-between hover:border-brand-500 hover:text-brand-700 transition">
                        <span class="truncate font-bold text-gray-700" x-text="selectedCustomer ? '👤 ' + selectedCustomer.name : '👤 Pelanggan Umum'"></span>
                        <span class="text-brand-600 text-[10px] font-bold">UBAH</span>
                    </button>
                </div>

                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1 px-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Potongan</label>
                        <button @click="toggleDiscountMode()"
                                class="text-[10px] font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                            <span x-text="manualDiscountMode ? 'Gunakan Kode Promo' : '+ Input Manual'"></span>
                        </button>
                    </div>

                    <div x-show="!manualDiscountMode">
                        <select x-model="selectedPromoId" @change="calculateTotal()"
                                class="w-full text-xs rounded border-gray-300 bg-white py-1.5 focus:ring-brand-500 text-brand-700 font-bold">
                            <option value="">🎟️ Tidak Ada Diskon</option>
                            @foreach($promotions as $promo)
                                <option value="{{ $promo->id }}" data-type="{{ $promo->discount_type }}" data-value="{{ $promo->value }}">
                                    {{ $promo->name }} ({{ $promo->discount_type == 'fixed' ? 'Rp' : '%' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="manualDiscountMode" style="display: none;" class="flex gap-1">
                        <select x-model="manualType" @change="calculateTotal()"
                                class="w-1/3 text-xs rounded border-gray-300 bg-white font-bold py-1.5 focus:ring-brand-500">
                            <option value="fixed">Rp</option>
                            <option value="percentage">%</option>
                        </select>
                        <input type="number" x-model.number="manualValue" @input="calculateTotal()"
                               placeholder="Nominal..."
                               class="w-2/3 text-xs rounded border-brand-300 focus:border-brand-500 font-bold py-1.5 text-right text-red-600">
                    </div>
                </div>

                <div class="space-y-1 mb-3 text-sm px-1 border-t border-dashed border-gray-300 pt-2">
                    <div class="flex justify-between text-gray-500 text-xs">
                        <span>Subtotal:</span>
                        <span x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-red-500 text-xs" x-show="discount > 0">
                        <span>Diskon:</span>
                        <span x-text="'- ' + formatRupiah(discount)"></span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="font-bold text-gray-800">Total Akhir</span>
                        <span class="font-black text-xl text-brand-900" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
                        <input type="number" x-model.number="paidAmount"
                               class="w-full pl-7 pr-2 py-2.5 rounded-lg border-gray-300 focus:ring-brand-500 font-bold text-sm"
                               placeholder="0">
                    </div>
                    <button @click="submitTransaction()"
                            :disabled="cart.length === 0 || paidAmount < grandTotal || isLoading"
                            class="px-4 py-2 bg-brand-900 text-white rounded-lg font-bold shadow hover:bg-black disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center justify-center min-w-[80px]">
                        <span x-show="!isLoading">BAYAR</span>
                        <span x-show="isLoading" class="animate-spin">⌛</span>
                    </button>
                </div>

                <div class="text-right mt-1 h-4">
                    <span class="text-xs font-bold text-green-600" x-show="paidAmount >= grandTotal && grandTotal > 0">
                        Kembali: <span x-text="formatRupiah(paidAmount - grandTotal)"></span>
                    </span>
                    <span class="text-[10px] text-red-500" x-show="paidAmount < grandTotal && paidAmount > 0">
                        Kurang: <span x-text="formatRupiah(grandTotal - paidAmount)"></span>
                    </span>
                </div>
            </div>
        </div>

        <div x-show="showCustomerModal"
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showCustomerModal"
                     x-transition.opacity
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showCustomerModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCustomerModal"
                     x-transition.scale
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Cari Pelanggan</h3>
                            <button @click="showCustomerModal = false" class="text-gray-400 hover:text-gray-500">✕</button>
                        </div>

                        <div class="mb-4">
                            <input type="text" x-model="customerSearch" x-ref="cusSearchInput"
                                   placeholder="Ketik Nama atau No HP..."
                                   class="w-full rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 font-bold">
                        </div>

                        <div class="h-60 overflow-y-auto border border-gray-100 rounded-lg">
                            <template x-for="cus in filteredCustomers" :key="cus.id">
                                <div @click="selectCustomer(cus)"
                                     class="p-3 hover:bg-brand-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center group">
                                    <div>
                                        <div class="font-bold text-gray-800" x-text="cus.name"></div>
                                        <div class="text-xs text-gray-500" x-text="cus.phone || '-'"></div>
                                    </div>
                                    <div class="text-brand-600 opacity-0 group-hover:opacity-100 text-xs font-bold">Pilih</div>
                                </div>
                            </template>

                            <div x-show="filteredCustomers.length === 0" class="p-4 text-center text-gray-400 text-sm">
                                Pelanggan tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="selectCustomer(null)" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Reset ke Umum
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', (products, promotions, customers) => ({
                products: products,
                promotions: promotions,
                customers: customers,

                search: '',
                filterType: 'goods', // DEFAULT GANTI KE 'GOODS' (SPAREPART)
                cart: [],

                // Logic Pelanggan
                showCustomerModal: false,
                customerSearch: '',
                selectedCustomer: null,
                customerId: '',

                // Logic Promo & Diskon Manual
                manualDiscountMode: false,
                manualType: 'fixed', // 'fixed' or 'percentage'
                manualValue: '',

                selectedPromoId: '',
                subtotal: 0,
                discount: 0,
                grandTotal: 0,
                paidAmount: '',
                isLoading: false,

                // Computed Products
                get filteredProducts() {
                    let items = this.products;
                    // Filter Type (Always Active now)
                    items = items.filter(p => p.type === this.filterType);

                    if (this.search !== '') {
                        items = items.filter(p =>
                            p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            p.product_code.toLowerCase().includes(this.search.toLowerCase())
                        );
                    }
                    return items;
                },

                get filteredCustomers() {
                    if (this.customerSearch === '') return this.customers;
                    return this.customers.filter(c =>
                        c.name.toLowerCase().includes(this.customerSearch.toLowerCase()) ||
                        (c.phone && c.phone.includes(this.customerSearch))
                    );
                },

                // Functions
                openCustomerModal() {
                    this.showCustomerModal = true;
                    this.customerSearch = '';
                    this.$nextTick(() => { this.$refs.cusSearchInput.focus(); });
                },

                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.customerId = customer ? customer.id : '';
                    this.showCustomerModal = false;
                },

                toggleDiscountMode() {
                    this.manualDiscountMode = !this.manualDiscountMode;
                    this.selectedPromoId = '';
                    this.manualValue = '';
                    this.calculateTotal();
                },

                addToCart(product) {
                    if (product.type === 'goods' && product.stock_quantity <= 0) {
                        return Swal.fire({ icon: 'error', title: 'Stok Habis!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                    }
                    let existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        if (product.type === 'goods' && existingItem.qty >= product.stock_quantity) {
                            return Swal.fire({ icon: 'warning', title: 'Stok Limit!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                        }
                        existingItem.qty++;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.sell_price,
                            type: product.type,
                            max_stock: product.stock_quantity,
                            qty: 1
                        });
                    }
                    this.calculateTotal();
                },

                updateQty(index, amount) {
                    let item = this.cart[index];
                    let newQty = item.qty + amount;
                    if (newQty <= 0) {
                        this.removeFromCart(index);
                    } else {
                        if (item.type === 'goods' && newQty > item.max_stock) {
                            return Swal.fire({ icon: 'warning', title: 'Stok Limit!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                        }
                        item.qty = newQty;
                        this.calculateTotal();
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.calculateTotal();
                },

                resetCart() {
                    if(confirm('Reset keranjang?')) {
                        this.cart = [];
                        this.paidAmount = '';
                        this.selectedPromoId = '';
                        this.selectCustomer(null);
                        this.manualDiscountMode = false;
                        this.manualValue = '';
                        this.calculateTotal();
                    }
                },

                calculateTotal() {
                    this.subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    this.discount = 0;

                    // Logic Diskon Manual vs Promo
                    if (this.manualDiscountMode) {
                        let val = parseFloat(this.manualValue) || 0;
                        if (this.manualType === 'fixed') {
                            this.discount = val;
                        } else {
                            this.discount = this.subtotal * (val / 100);
                        }
                    } else {
                        if (this.selectedPromoId) {
                            let promo = this.promotions.find(p => p.id == this.selectedPromoId);
                            if (promo) {
                                if (promo.discount_type === 'fixed') {
                                    this.discount = parseFloat(promo.value);
                                } else {
                                    this.discount = this.subtotal * (parseFloat(promo.value) / 100);
                                }
                            }
                        }
                    }

                    // Safety Check
                    if (this.discount > this.subtotal) this.discount = this.subtotal;
                    this.grandTotal = Math.max(0, this.subtotal - this.discount);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
                },

                async submitTransaction() {
                    this.isLoading = true;
                    try {
                        let res = await fetch("{{ route('pos.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                customer_id: this.customerId,
                                paid_amount: this.paidAmount,
                                // Kirim salah satu: Promo ID atau Manual Discount
                                promotion_id: !this.manualDiscountMode ? this.selectedPromoId : null,
                                manual_discount: this.manualDiscountMode ? this.discount : 0
                            })
                        });

                        let data = await res.json();

                        if (data.status === 'success') {
                            // TAMPILKAN POPUP DENGAN OPSI CETAK
                            Swal.fire({
                                title: 'Transaksi Berhasil!',
                                html: `
                                    <div class="text-center">
                                        <p class="text-sm text-gray-500 mb-1">Kembalian Anda</p>
                                        <h2 class="text-3xl font-black text-green-600 mb-4">${this.formatRupiah(data.change)}</h2>
                                    </div>
                                `,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#111827', // Hitam
                                cancelButtonColor: '#9ca3af',  // Abu-abu
                                confirmButtonText: '🖨️ Cetak Struk',
                                cancelButtonText: 'Tutup & Transaksi Baru',
                                reverseButtons: true,
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Buka Struk
                                    let printWindow = window.open(`/pos/print/${data.invoice_code}`, '_blank', 'width=400,height=600');
                                    // Reload
                                    setTimeout(() => { window.location.reload(); }, 1000);
                                } else {
                                    window.location.reload();
                                }
                            });

                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        Swal.fire('Gagal!', error.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
