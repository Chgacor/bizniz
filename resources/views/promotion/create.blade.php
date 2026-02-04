<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Buat Promo Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen" x-data="{ type: 'transaction', discountType: 'percentage' }">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <form action="{{ route('promotions.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Promo</label>
                        <input type="text" name="name" class="w-full rounded-lg border-gray-300 focus:ring-brand-500" placeholder="Contoh: Diskon Kemerdekaan" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-brand-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Target Diskon</label>
                            <select name="type" x-model="type" class="w-full rounded-lg border-gray-300 focus:ring-brand-500">
                                <option value="transaction">Total Transaksi</option>
                                <option value="service">Jasa Service</option>
                                <option value="product">Barang / Sparepart</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Potongan</label>
                            <select name="discount_type" x-model="discountType" class="w-full rounded-lg border-gray-300 focus:ring-brand-500">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Besar Diskon</label>
                        <div class="relative">
                            <span x-show="discountType == 'fixed'" class="absolute left-3 top-2.5 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="value" class="w-full rounded-lg border-gray-300 focus:ring-brand-500 font-bold text-lg"
                                   :class="discountType == 'fixed' ? 'pl-10' : ''" placeholder="0">
                            <span x-show="discountType == 'percentage'" class="absolute right-4 top-2.5 text-gray-500 font-bold">%</span>
                        </div>
                        <p x-show="type == 'transaction'" class="text-xs text-gray-500 mt-1">*Diskon akan memotong total akhir belanja.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-gray-400 italic text-center">Kosongkan tanggal jika promo berlaku selamanya.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('promotions.index') }}" class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-brand-900 text-white font-bold rounded-lg shadow hover:bg-black transition">Simpan Promo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
