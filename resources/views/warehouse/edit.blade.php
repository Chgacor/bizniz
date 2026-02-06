<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">Edit Produk</h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen"
         x-data="productEdit({
             type: '{{ $product->type }}',
             name: '{{ addslashes($product->name) }}',
             buy: {{ $product->buy_price }},
             sell: {{ $product->sell_price }},
             stock: {{ $product->stock_quantity }},
             existingImage: '{{ $product->image_path ? asset('storage/'.$product->image_path) : null }}'
         })">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

                <div class="bg-brand-900 px-6 py-4 border-b border-brand-800 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg">Edit: {{ $product->name }}</h3>
                    <span class="text-brand-200 text-xs font-mono bg-brand-800 px-2 py-1 rounded" x-text="type === 'goods' ? 'MODE: BARANG' : 'MODE: JASA'"></span>
                </div>

                <form action="{{ route('warehouse.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col items-center justify-center transition-all"
                                           :class="type === 'goods' ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="type" value="goods" class="hidden" x-model="type">
                                        <span class="text-2xl mb-1">📦</span><span class="font-bold text-sm">Barang</span>
                                    </label>
                                    <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col items-center justify-center transition-all"
                                           :class="type === 'service' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="type" value="service" class="hidden" x-model="type">
                                        <span class="text-2xl mb-1">🔧</span><span class="font-bold text-sm">Jasa</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                                    <input type="text" name="name" x-model="name" required class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-bold">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div x-show="type === 'goods'" x-transition>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Barang</label>
                                        <input type="text" value="{{ $product->product_code ?? '(Auto Generate)' }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-600 font-mono text-sm font-bold cursor-not-allowed">
                                    </div>

                                    <div class="relative" :class="type === 'goods' ? '' : 'col-span-2'" x-data="{ open: false, selected: '{{ $product->category }}', options: {{ $categories->pluck('name') }} }">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                                        <input type="hidden" name="category" :value="selected">
                                        <input type="text" x-model="selected" @focus="open = true" @input="open = true" class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-bold text-sm" placeholder="Pilih...">
                                        <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto" style="display:none;">
                                            <template x-for="opt in options.filter(i => i.toLowerCase().includes(selected.toLowerCase()))"><div @click="selected = opt; open = false" class="px-4 py-2 hover:bg-gray-100 cursor-pointer" x-text="opt"></div></template>
                                            <div x-show="selected && !options.includes(selected)" @click="open = false" class="px-4 py-2 bg-brand-50 font-bold text-brand-700 cursor-pointer">+ Pakai Baru</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-show="type === 'goods'" class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                <label class="block text-sm font-bold text-orange-800 mb-1">Stok Saat Ini</label>
                                <input type="number" name="stock_quantity" x-model="stock" class="w-full rounded-lg border-orange-300 focus:ring-orange-500 text-center font-bold text-lg">
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <h4 class="font-bold text-gray-700 mb-4">💰 Edit Harga</h4>
                                <div x-show="type === 'goods'">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Beli</label>
                                    <input type="number" x-model.number="buy" @input="calcSell()" name="buy_price" class="w-full mb-4 rounded-lg border-gray-300">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Margin (%)</label>
                                    <input type="number" x-model.number="margin" @input="calcSell()" class="w-full mb-4 rounded-lg border-gray-300">
                                </div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual</label>
                                <input type="number" x-model.number="sell" @input="calcMargin()" name="sell_price" class="w-full rounded-lg border-brand-500 ring-2 ring-brand-100 font-mono text-2xl font-bold text-brand-900">
                            </div>

                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative">
                                <input type="file" name="image" id="fileInput" class="hidden" @change="previewImage($event)">
                                <div x-show="!imgPreview" @click="document.getElementById('fileInput').click()"><p class="text-sm text-gray-600 font-bold">Ganti Foto</p></div>
                                <div x-show="imgPreview" class="relative">
                                    <img :src="imgPreview" class="max-h-40 mx-auto rounded-lg shadow-sm">
                                    <button type="button" @click="removeImage()" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('warehouse.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-bold">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-brand-900 text-white rounded-lg font-bold shadow-lg hover:bg-black">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productEdit', (init) => ({
                type: init.type, name: init.name, buy: init.buy, sell: init.sell, stock: init.stock, margin: 0, imgPreview: init.existingImage,
                init() { this.calcMargin(); },
                calcSell() { if(this.buy > 0) this.sell = Math.ceil((this.buy + (this.buy * (this.margin/100))) / 500) * 500; },
                calcMargin() { if(this.buy > 0 && this.sell > 0) this.margin = parseFloat((((this.sell - this.buy)/this.buy)*100).toFixed(1)); else this.margin = 0; },
                previewImage(e) { if(e.target.files[0]) this.imgPreview = URL.createObjectURL(e.target.files[0]); },
                removeImage() { this.imgPreview = null; document.getElementById('fileInput').value = ''; }
            }));
        });
    </script>
</x-app-layout>
