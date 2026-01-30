<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Edit Produk') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden"
                 x-data="editProductLogic({
                     buy: {{ $product->buy_price }},
                     sell: {{ $product->sell_price }}
                 })">

                <div class="bg-brand-900 px-6 py-4 border-b border-brand-800 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Barang: {{ $product->name }}
                    </h3>
                </div>

                <form action="{{ route('warehouse.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    @method('PUT') <div class="grid grid-cols-12 gap-8">

                        <div class="col-span-12 md:col-span-8 space-y-6">

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                                <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 h-12 text-lg">
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode Barang</label>
                                    <input type="text" name="product_code" readonly value="{{ $product->product_code }}"
                                           class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 font-mono font-bold cursor-not-allowed">
                                    <p class="text-xs text-gray-400 mt-1">*Kode barang sebaiknya tidak diubah.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                                    <input type="text" name="category" required value="{{ old('category', $product->category) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
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
                                <label class="block text-sm font-bold text-gray-700 mb-2">Stok Saat Ini</label>
                                <input type="number" name="stock_quantity" required value="{{ $product->stock_quantity }}"
                                       class="w-full rounded-lg border-gray-300 focus:ring-brand-500 text-center text-lg font-bold">
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Jika angka diubah, sistem akan otomatis mencatat sebagai <span class="font-bold">Koreksi Stok</span>.
                                </p>
                            </div>

                            <div class="border rounded-lg p-4 bg-white">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk</label>

                                @if($product->image_path)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="Current Image" class="w-full h-32 object-cover rounded-lg border">
                                        <p class="text-xs text-gray-400 mt-1 text-center">Foto saat ini</p>
                                    </div>
                                @endif

                                <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                <p class="text-xs text-gray-400 mt-2">Biarkan kosong jika tidak ingin mengubah foto.</p>
                            </div>

                        </div>

                        <div class="col-span-12 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                            <a href="{{ route('warehouse.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50">Batal</a>
                            <button type="submit" class="px-8 py-3 bg-brand-900 text-white rounded-lg font-bold hover:bg-black shadow-lg">Update Produk</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('editProductLogic', (initialData) => ({
                buy: initialData.buy,
                sell: initialData.sell,
                margin: 0,

                init() {
                    // Hitung margin awal saat halaman dimuat
                    this.calcMargin();
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
