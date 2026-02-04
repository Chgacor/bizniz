<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen" x-data="productForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

                <div class="bg-brand-900 px-6 py-4 border-b border-brand-800 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Formulir Produk
                    </h3>
                    <span class="text-brand-200 text-xs font-mono bg-brand-800 px-2 py-1 rounded" x-text="type === 'goods' ? 'MODE: BARANG' : 'MODE: JASA'"></span>
                </div>

                <form action="{{ route('warehouse.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col items-center justify-center transition-all"
                                           :class="type === 'goods' ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="type" value="goods" class="hidden" x-model="type">
                                        <span class="text-2xl mb-1">📦</span>
                                        <span class="font-bold text-sm">Barang</span>
                                    </label>
                                    <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col items-center justify-center transition-all"
                                           :class="type === 'service' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="type" value="service" class="hidden" x-model="type">
                                        <span class="text-2xl mb-1">🔧</span>
                                        <span class="font-bold text-sm">Jasa</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                                    <input type="text" name="name" x-model="name" @input="generatePreviewCode()" required
                                           class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-bold"
                                           placeholder="Contoh: Kampas Rem Depan">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Preview ID</label>
                                        <input type="text" :value="previewCode" readonly
                                               class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 font-mono text-sm font-bold">
                                        <p class="text-[10px] text-gray-400 mt-1">*ID Asli digenerate server</p>
                                    </div>

                                    <div class="relative" x-data="{
                                        open: false,
                                        search: '',
                                        selected: '',
                                        options: {{ $categories->pluck('name') }}
                                    }" @click.away="open = false">

                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>

                                        <input type="hidden" name="category" :value="selected">

                                        <div class="relative">
                                            <input type="text" x-model="selected"
                                                   @focus="open = true"
                                                   @input="open = true"
                                                   class="w-full rounded-lg border-gray-300 focus:ring-brand-500 focus:border-brand-500 text-sm font-bold"
                                                   placeholder="Pilih atau Ketik Baru..." autocomplete="off">

                                            <button type="button" @click="open = !open" class="absolute right-0 top-0 bottom-0 px-3 text-gray-400 hover:text-gray-600">
                                                <svg class="w-4 h-4 transition transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>

                                        <div x-show="open" x-transition.origin.top.left
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto custom-scrollbar" style="display: none;">

                                            <template x-for="option in options.filter(i => i.toLowerCase().includes(selected.toLowerCase()))" :key="option">
                                                <div @click="selected = option; open = false"
                                                     class="px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700 cursor-pointer font-medium transition">
                                                    <span x-text="option"></span>
                                                </div>
                                            </template>

                                            <div x-show="selected.length > 0 && !options.includes(selected)"
                                                 @click="open = false"
                                                 class="px-4 py-2 text-sm text-brand-600 bg-brand-50 cursor-pointer border-t border-gray-100 font-bold">
                                                + Gunakan kategori baru: "<span x-text="selected"></span>"
                                            </div>

                                            <div x-show="options.length === 0" class="px-4 py-3 text-xs text-gray-400 text-center">
                                                Belum ada kategori tersimpan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-show="type === 'goods'" class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                <label class="block text-sm font-bold text-orange-800 mb-1">Stok Awal</label>
                                <input type="number" name="stock_quantity" value="0"
                                       class="w-full rounded-lg border-orange-300 focus:ring-orange-500 text-center font-bold text-lg">
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                                    <span class="bg-green-100 text-green-600 p-1.5 rounded mr-2">💰</span>
                                    Harga
                                </h4>

                                <div x-show="type === 'goods'" class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Beli</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-400 font-bold">Rp</span>
                                        <input type="number" x-model.number="buy" @input="calcSell()" name="buy_price"
                                               class="w-full pl-10 rounded-lg border-gray-300 focus:ring-brand-500 font-mono">
                                    </div>
                                </div>

                                <div x-show="type === 'goods'" class="mb-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Margin (%)</label>
                                    <div class="relative">
                                        <input type="number" x-model.number="margin" @input="calcSell()"
                                               class="w-full rounded-lg border-gray-300 focus:ring-brand-500 h-10 text-blue-600 font-bold pr-8">
                                        <span class="absolute right-3 top-2 text-blue-600 font-bold">%</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-gray-400 font-bold">Rp</span>
                                        <input type="number" x-model.number="sell" @input="calcMargin()" name="sell_price" required
                                               class="w-full pl-10 rounded-lg border-brand-500 ring-2 ring-brand-100 h-12 font-mono text-2xl font-bold text-brand-900">
                                    </div>
                                </div>
                            </div>

                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative"
                                 @dragover.prevent="dragover = true"
                                 @dragleave.prevent="dragover = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="dragover ? 'bg-blue-50 border-blue-400' : ''">

                                <input type="file" name="image" id="fileInput" class="hidden" @change="previewImage($event)">

                                <div x-show="!imgPreview" @click="document.getElementById('fileInput').click()">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="mt-1 text-sm text-gray-600">Drag & Drop atau Klik</p>
                                </div>

                                <div x-show="imgPreview" class="relative">
                                    <img :src="imgPreview" class="max-h-40 mx-auto rounded-lg shadow-sm">
                                    <button type="button" @click="removeImage()" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
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
            Alpine.data('productForm', () => ({
                type: 'goods',
                name: '',
                previewCode: '---',
                buy: 0,
                sell: 0,
                margin: 20,
                dragover: false,
                imgPreview: null,

                generatePreviewCode() {
                    if(!this.name) { this.previewCode = '---'; return; }
                    let words = this.name.replace(/[^a-zA-Z0-9 ]/g, '').toUpperCase().split(' ');
                    let initials = words.map(w => w[0]).join('').substring(0,3);
                    if(initials.length < 2) initials = this.name.substring(0,2).toUpperCase();
                    this.previewCode = initials + 'XXXXXXXX';
                },
                calcSell() {
                    if(this.buy > 0) {
                        this.sell = Math.ceil((this.buy + (this.buy * (this.margin/100))) / 500) * 500;
                    }
                },
                calcMargin() {
                    if(this.buy > 0 && this.sell > 0) {
                        this.margin = parseFloat((((this.sell - this.buy)/this.buy)*100).toFixed(1));
                    }
                },
                previewImage(event) {
                    const file = event.target.files[0];
                    if(file) {
                        this.imgPreview = URL.createObjectURL(file);
                    }
                },
                handleDrop(event) {
                    this.dragover = false;
                    const file = event.dataTransfer.files[0];
                    if(file) {
                        document.getElementById('fileInput').files = event.dataTransfer.files;
                        this.imgPreview = URL.createObjectURL(file);
                    }
                },
                removeImage() {
                    this.imgPreview = null;
                    document.getElementById('fileInput').value = '';
                }
            }));
        });
    </script>
</x-app-layout>
