<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" x-data="productLogic()">

                <div class="bg-brand-900 px-6 py-4 border-b border-brand-800">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Formulir Input Barang
                    </h3>
                </div>

                <form action="{{ route('warehouse.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf

                    <div class="grid grid-cols-12 gap-8">

                        <div class="col-span-12 md:col-span-8 space-y-6">

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                                <input type="text" x-model="name" @input="generateCode()" name="name" required
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 h-12 text-lg"
                                       placeholder="Contoh: Kopi Kapal Api">
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode Barang (Auto)</label>
                                    <div class="flex">
                                        <input type="text" x-model="code" name="product_code" readonly
                                               class="w-full rounded-l-lg border-gray-300 bg-gray-100 text-gray-600 font-mono font-bold">
                                        <button type="button" @click="generateCode(true)" class="bg-gray-200 px-3 rounded-r-lg hover:bg-gray-300 border border-l-0 border-gray-300 text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                                    <input type="text" name="category" required
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                           placeholder="Ketik kategori...">
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <div class="bg-brand-50 p-6 rounded-xl border border-brand-100">
                                <h4 class="text-brand-800 font-bold mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Kalkulator Harga & Margin
                                </h4>

                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Modal</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2.5 text-gray-400 font-bold">Rp</span>
                                            <input type="number" x-model.number="buy" @input="calcSell()" name="buy_price" required
                                                   class="w-full pl-10 rounded-lg border-gray-300 focus:ring-brand-500 h-10 font-mono">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Margin (%)</label>
                                        <div class="relative">
                                            <input type="number" x-model.number="margin" @input="calcSell()"
                                                   class="w-full rounded-lg border-gray-300 focus:ring-brand-500 h-10 text-blue-600 font-bold">
                                            <span class="absolute right-8 top-2.5 text-gray-400 font-bold">%</span>
                                        </div>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual Final</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-3 text-gray-400 font-bold">Rp</span>
                                            <input type="number" x-model.number="sell" @input="calcMargin()" name="sell_price" required
                                                   class="w-full pl-10 rounded-lg border-brand-500 ring-2 ring-brand-100 h-12 font-mono text-xl font-bold text-gray-900">
                                        </div>
                                        <div class="mt-2 text-right">
                                            <span class="text-xs text-gray-500">Estimasi Profit:</span>
                                            <span class="text-sm font-bold text-green-600" x-text="formatRupiah(sell - buy)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-span-12 md:col-span-4 space-y-6">

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal</label>
                                <input type="number" name="stock_quantity" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-brand-500 text-center text-lg font-bold" value="0">
                            </div>

                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition relative">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4 flex text-sm text-gray-600 justify-center">
                                    <label class="relative cursor-pointer rounded-md font-medium text-brand-600 hover:text-brand-500">
                                        <span>Upload Foto</span>
                                        <input type="file" name="image" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>

                        </div>

                        <div class="col-span-12 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                            <a href="{{ route('warehouse.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50">Batal</a>
                            <button type="submit" class="px-8 py-3 bg-brand-900 text-white rounded-lg font-bold hover:bg-black shadow-lg">Simpan Produk</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productLogic', () => ({
                name: '',
                code: '',
                buy: 0,
                sell: 0,
                margin: 20,

                generateCode(force = false) {
                    if (!this.code || force) {
                        let date = new Date();
                        let d = String(date.getDate()).padStart(2,'0') + String(date.getMonth()+1).padStart(2,'0') + date.getFullYear();
                        let initials = this.name.split(' ').map(w => w[0]).join('').substring(0,3).toUpperCase();
                        this.code = (initials || 'BRG') + d;
                    }
                },
                calcSell() {
                    if(this.buy > 0) this.sell = Math.round(this.buy + (this.buy * (this.margin/100)));
                },
                calcMargin() {
                    if(this.buy > 0 && this.sell > 0) this.margin = parseFloat((((this.sell - this.buy)/this.buy)*100).toFixed(2));
                },
                formatRupiah(n) {
                    return new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n);
                }
            }));
        });
    </script>
</x-app-layout>
