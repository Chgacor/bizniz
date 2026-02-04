<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Proses Retur Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen" x-data="returnSystem()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 mb-6">
                <div class="p-8 border-b border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Scan / Input Nomor Invoice</label>
                    <div class="flex gap-4">
                        <input type="text" x-model="invoiceCode" @keydown.enter="searchInvoice()"
                               class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-mono text-lg uppercase"
                               placeholder="Contoh: INV-20260201-123" autofocus>
                        <button @click="searchInvoice()" class="px-6 py-3 bg-brand-900 text-white font-bold rounded-lg hover:bg-black shadow-lg">
                            Cari Transaksi
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="transaction" x-transition class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                <div class="bg-brand-50 px-6 py-4 border-b border-brand-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-brand-900 font-bold text-lg">Detail Transaksi</h3>
                        <p class="text-xs text-brand-600" x-text="'Tanggal: ' + (transaction ? transaction.created_at : '')"></p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Pelanggan</div>
                        <div class="font-bold text-gray-800" x-text="transaction?.customer?.name || 'Umum'"></div>
                    </div>
                </div>

                <div class="p-8">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="text-xs font-bold text-gray-500 uppercase border-b border-gray-200">
                            <th class="py-3">Produk</th>
                            <th class="py-3 text-center">Qty Beli</th>
                            <th class="py-3 text-center">Sudah Retur</th>
                            <th class="py-3 text-center">Bisa Retur</th>
                            <th class="py-3 w-32 text-center">Qty Retur</th>
                            <th class="py-3 w-40 text-center">Kondisi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr :class="item.available_qty === 0 ? 'opacity-50 bg-gray-50' : ''">
                                <td class="py-4 font-bold text-gray-700" x-text="item.product_name"></td>
                                <td class="py-4 text-center text-gray-600" x-text="item.sold_qty"></td>
                                <td class="py-4 text-center text-orange-600 font-bold" x-text="item.returned_qty"></td>
                                <td class="py-4 text-center text-green-600 font-bold" x-text="item.available_qty"></td>
                                <td class="py-4">
                                    <input type="number" x-model.number="item.return_input"
                                           :disabled="item.available_qty === 0"
                                           class="w-full rounded border-gray-300 text-center font-bold focus:ring-brand-500"
                                           min="0" :max="item.available_qty">
                                </td>
                                <td class="py-4 px-2">
                                    <select x-model="item.condition" :disabled="item.available_qty === 0"
                                            class="w-full rounded border-gray-300 text-sm focus:ring-brand-500">
                                        <option value="good">Layak Jual (Good)</option>
                                        <option value="bad">Rusak (Bad)</option>
                                    </select>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Retur</label>
                        <textarea x-model="reason" class="w-full rounded-lg border-gray-300 focus:ring-brand-500" rows="2" placeholder="Cacat pabrik / Salah beli..."></textarea>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <button @click="resetForm()" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold">Batal</button>
                        <button @click="submitReturn()" class="px-8 py-3 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 shadow-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Proses Retur
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('returnSystem', () => ({
                invoiceCode: '',
                transaction: null,
                items: [],
                reason: '',

                async searchInvoice() {
                    if (!this.invoiceCode) return Swal.fire('Error', 'Masukkan Nomor Invoice!', 'warning');

                    try {
                        let res = await fetch(`/returns/search?code=${this.invoiceCode}`);
                        let data = await res.json();

                        if (data.status === 'success') {
                            this.transaction = data.transaction;
                            this.items = data.items.map(i => ({ ...i, return_input: 0, condition: 'good' }));
                        } else {
                            Swal.fire('Tidak Ditemukan', data.message, 'error');
                            this.transaction = null;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },
                async submitReturn() {
                    let itemsToReturn = this.items.filter(i => i.return_input > 0);

                    if (itemsToReturn.length === 0) {
                        return Swal.fire('Peringatan', 'Belum ada barang yang dipilih untuk diretur.', 'warning');
                    }

                    // Validasi Client Side
                    for (let item of itemsToReturn) {
                        if (item.return_input > item.available_qty) {
                            return Swal.fire('Error', `Jumlah retur ${item.product_name} melebihi batas!`, 'error');
                        }
                    }

                    let payload = {
                        transaction_id: this.transaction.id,
                        reason: this.reason,
                        items: itemsToReturn.map(i => ({
                            product_id: i.product_id,
                            quantity: i.return_input,
                            condition: i.condition,
                            price: i.price
                        }))
                    };

                    try {
                        let res = await fetch("{{ route('returns.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });
                        let data = await res.json();

                        if (data.status === 'success') {
                            Swal.fire('Sukses', 'Retur berhasil disimpan & stok diperbarui.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (e) {
                        Swal.fire('Gagal', e.message, 'error');
                    }
                },
                resetForm() {
                    this.transaction = null;
                    this.invoiceCode = '';
                    this.items = [];
                    this.reason = '';
                }
            }));
        });
    </script>
</x-app-layout>
