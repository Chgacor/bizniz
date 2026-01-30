<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Konfigurasi Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{ activeTab: 'general' }" class="flex flex-col md:flex-row gap-6">

                <div class="w-full md:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl sticky top-6">
                        <div class="p-4 bg-brand-900 text-white font-bold text-sm rounded-t-xl">
                            MENU PENGATURAN
                        </div>
                        <nav class="flex flex-col p-2 space-y-1">
                            <button @click="activeTab = 'general'"
                                    :class="activeTab === 'general' ? 'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                    class="text-left px-4 py-3 text-sm rounded-md transition flex items-center">
                                <span class="mr-3">🏢</span> Umum & Bisnis
                            </button>
                            <button @click="activeTab = 'finance'"
                                    :class="activeTab === 'finance' ? 'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                    class="text-left px-4 py-3 text-sm rounded-md transition flex items-center">
                                <span class="mr-3">💰</span> Keuangan & Pajak
                            </button>
                            <button @click="activeTab = 'pos'"
                                    :class="activeTab === 'pos' ? 'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                    class="text-left px-4 py-3 text-sm rounded-md transition flex items-center">
                                <span class="mr-3">🖨️</span> Struk & POS
                            </button>
                            <button @click="activeTab = 'backup'"
                                    :class="activeTab === 'backup' ? 'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                    class="text-left px-4 py-3 text-sm rounded-md transition flex items-center">
                                <span class="mr-3">💾</span> Database Backup
                            </button>
                        </nav>
                    </div>
                </div>

                <div class="w-full md:w-3/4">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div x-show="activeTab === 'general'" x-transition.opacity class="bg-white shadow-sm sm:rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Bisnis</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Bisnis</label>
                                    <input type="text" name="business_name" value="{{ $settings['business_name'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Alamat Toko</label>
                                    <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ $settings['address'] ?? '' }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                        <input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email Toko</label>
                                        <input type="email" name="email" value="{{ $settings['email'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeTab === 'finance'" style="display: none;" x-transition.opacity class="bg-white shadow-sm sm:rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Keuangan & Mata Uang</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Simbol Mata Uang</label>
                                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? 'Rp' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pajak PPN (%)</label>
                                        <input type="number" name="tax_rate" value="{{ $settings['tax_rate'] ?? '0' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <p class="text-xs text-gray-500 mt-1">Isi 0 jika tidak ada pajak.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeTab === 'pos'" style="display: none;" x-transition.opacity class="bg-white shadow-sm sm:rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Pengaturan Struk (Receipt)</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Header Struk (Atas)</label>
                                    <input type="text" name="receipt_header" value="{{ $settings['receipt_header'] ?? '' }}" placeholder="Contoh: Selamat Datang di Bizniz" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Footer Struk (Bawah)</label>
                                    <textarea name="receipt_footer" rows="2" placeholder="Contoh: Barang yang dibeli tidak dapat dikembalikan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $settings['receipt_footer'] ?? '' }}</textarea>
                                </div>
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" name="show_logo_on_receipt" value="1" class="rounded border-gray-300 text-brand-600 shadow-sm focus:border-brand-300 focus:ring focus:ring-brand-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Tampilkan Logo di Struk</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeTab !== 'backup'" class="mt-6 flex justify-end">
                            <button type="submit" class="bg-brand-900 hover:bg-black text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                                Simpan Konfigurasi
                            </button>
                        </div>
                    </form>

                    <div x-show="activeTab === 'backup'" style="display: none;" x-transition.opacity class="bg-white shadow-sm sm:rounded-xl p-6 border border-brand-200">
                        <div class="flex items-start">
                            <div class="bg-brand-100 p-3 rounded-full mr-4">
                                <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Backup Database</h3>
                                <p class="text-sm text-gray-500 mt-1">Unduh salinan lengkap database SQL Anda. Simpan file ini di tempat aman.</p>

                                <a href="{{ route('settings.backup') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    ⬇️ Download SQL Backup
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
