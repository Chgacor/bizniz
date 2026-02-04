<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Input Barang Masuk (Pembelian)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen" x-data="purchaseForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

                <div class="bg-brand-900 px-6 py-4 border-b border-brand-800 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Form Purchase Invoice
                    </h3>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Nota / Invoice</label>
                            <input type="text" x-model="form.invoice_number" class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-mono font-bold uppercase" placeholder="INV-001">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Supplier</label>
                            <input type="text" x-model="form.supplier_name" class="w-full rounded-lg border-gray-300 focus:ring-brand-500" placeholder="Toko Sparepart Jaya">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Masuk</label>
                            <input type="date" x-model="form.invoice_date" class="w-full rounded-lg border-gray-300 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="border rounded-xl overflow-hidden border-gray-200 mb-6">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700 font-bold uppercase">
                            <tr>
                                <th class="px-4 py-3 w-1/3">Nama Barang</th>
                                <th class="px-4 py-3 text-right">Harga Beli (Satuan)</th>
                                <th class="px-4 py-3 text-center">Qty Masuk</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in form.items" :key="index">
                                <tr>
                                    <td class="px-4 py-2 relative">
                                        <input type="text" x-model="item.product_name"
                                               @input.debounce.300ms="searchProduct(index, $event.target.value)"
                                               @focus="item.showDropdown = true"
                                               @click.away="item.showDropdown = false"
                                               class="w-full rounded border-gray-300 focus:ring-brand-500 text-sm"
                                               placeholder="Ketik nama barang...">

                                        <div x-show="item.showDropdown && item.searchResults.length > 0"
                                             class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            <template x-for="result in item.searchResults" :key="result.id">
                                                <div @click="selectProduct(index, result)"
                                                     class="px-4 py-2 hover:bg-brand-50 cursor-pointer text-sm border-b border-gray-50">
                                                    <div class="font-bold text-gray-800" x-text="result.name"></div>
                                                    <div class="text-xs text-gray-500 flex justify-between">
                                                        <span x-text="result.product_code"></span>
                                                        <span>Stok Saat Ini: <b x-text="result.stock_quantity"></b></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" x-model.number="item.buy_price" class="w-full rounded border-gray-300 text-right font-mono text-sm">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" x-model.number="item.quantity" class="w-full rounded border-gray-300 text-center font-bold text-sm">
                                    </td>
                                    <td class="px-4 py-2 text-right font-bold text-gray-700">
                                        <span x-text="formatRupiah(item.buy_price * item.quantity)"></span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 font-bold">×</button>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold text-gray-800">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right">TOTAL INVOICE</td>
                                <td class="px-4 py-3 text-right text-lg text-brand-900" x-text="formatRupiah(calculateTotal())"></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="p-3 bg-gray-50 border-t border-gray-200">
                            <button @click="addItem()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-100 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50">Batal</a>
                        <button @click="submitInvoice()"
                                :disabled="isLoading"
                                class="px-8 py-3 bg-brand-900 text-white rounded-lg font-bold hover:bg-black shadow-lg flex items-center disabled:opacity-50">
                            <span x-show="isLoading" class="mr-2 animate-spin">⌛</span>
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Stok Masuk'"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseForm', () => ({
                isLoading: false,
                form: {
                    invoice_number: '',
                    supplier_name: '',
                    invoice_date: new Date().toISOString().split('T')[0],
                    items: [{ product_id: null, product_name: '', buy_price: 0, quantity: 1, showDropdown: false, searchResults: [] }]
                },

                addItem() {
                    this.form.items.push({ product_id: null, product_name: '', buy_price: 0, quantity: 1, showDropdown: false, searchResults: [] });
                },
                removeItem(index) {
                    if (this.form.items.length > 1) {
                        this.form.items.splice(index, 1);
                    }
                },
                async searchProduct(index, query) {
                    if (query.length < 2) return;
                    let res = await fetch(`/purchase/search-products?query=${query}`);
                    let data = await res.json();
                    this.form.items[index].searchResults = data;
                    this.form.items[index].showDropdown = true;
                },
                selectProduct(index, product) {
                    this.form.items[index].product_id = product.id;
                    this.form.items[index].product_name = product.name;
                    this.form.items[index].buy_price = product.buy_price || 0;
                    this.form.items[index].showDropdown = false;
                },
                calculateTotal() {
                    return this.form.items.reduce((sum, item) => sum + (item.buy_price * item.quantity), 0);
                },
                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
                },
                async submitInvoice() {
                    if (!this.form.invoice_number || !this.form.supplier_name) {
                        return Swal.fire('Error', 'Nomor Invoice dan Supplier wajib diisi!', 'error');
                    }

                    this.isLoading = true;
                    try {
                        let res = await fetch("{{ route('purchase.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        });
                        let data = await res.json();

                        if (data.status === 'success') {
                            Swal.fire('Berhasil', data.message, 'success').then(() => {
                                window.location.href = "{{ route('warehouse.index') }}";
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        Swal.fire('Gagal', error.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
