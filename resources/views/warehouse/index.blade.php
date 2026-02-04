<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">

            {{-- BAGIAN ATAS: Judul & Tombol Aksi Cepat --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-200 pb-6">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ __('Gudang & Logistik') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Pusat kontrol stok, pembelian, dan retur barang.</p>
                </div>

                {{-- TOMBOL AKSI CEPAT (Stok Masuk & Retur) --}}
                <div class="flex flex-wrap gap-3">
                    @hasanyrole('Owner|Admin')
                    <div class="inline-flex rounded-lg shadow-sm" role="group">
                        <a href="{{ route('purchase.create') }}" class="px-4 py-2.5 text-sm font-bold bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 text-gray-700 flex items-center gap-2 transition">
                            <span class="text-green-600 bg-green-100 p-1 rounded">⬇️</span>
                            Stok Masuk
                        </a>
                        <a href="{{ route('purchase.index') }}" class="px-3 py-2.5 text-sm font-bold bg-white border-t border-b border-r border-gray-300 rounded-r-lg hover:bg-gray-50 text-gray-500 flex items-center" title="Riwayat Pembelian">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                    </div>
                    @endhasanyrole

                    <div class="inline-flex rounded-lg shadow-sm" role="group">
                        <a href="{{ route('returns.create') }}" class="px-4 py-2.5 text-sm font-bold bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 text-gray-700 flex items-center gap-2 transition">
                            <span class="text-red-600 bg-red-100 p-1 rounded">🔄</span>
                            Retur
                        </a>
                        <a href="{{ route('returns.index') }}" class="px-3 py-2.5 text-sm font-bold bg-white border-t border-b border-r border-gray-300 rounded-r-lg hover:bg-gray-50 text-gray-500 flex items-center" title="Riwayat Retur">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- BAGIAN BAWAH: Pencarian & Tambah Produk --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('warehouse.index') }}" class="relative w-full md:w-96 group">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama barang, kode, atau kategori..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition text-sm shadow-sm placeholder-gray-400">
                </form>

                <a href="{{ route('warehouse.create') }}" class="bg-black hover:bg-gray-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center text-sm gap-2 whitespace-nowrap w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Produk Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                     class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between animate-fade-in-down">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-white hover:text-green-100 font-bold">✕</button>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="bg-orange-100 text-orange-600 p-2 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Stok Barang</h3>
                            <p class="text-xs text-gray-500">Sparepart & Aksesoris Fisik</p>
                        </div>
                    </div>
                    <span class="bg-white text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200 shadow-sm">
                        {{ $goods->total() }} Item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100 bg-white">
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Status Stok</th>
                            <th class="px-6 py-4 text-right">Modal (HPP)</th>
                            <th class="px-6 py-4 text-right">Harga Jual</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($goods as $item)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative h-12 w-12 rounded-xl overflow-hidden border border-gray-200 bg-white flex-shrink-0">
                                            @if($item->image_path)
                                                <img class="h-full w-full object-cover" src="{{ asset('storage/' . $item->image_path) }}" alt="">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-50 text-gray-300 text-xl font-bold">
                                                    {{ substr($item->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm group-hover:text-brand-600 transition">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-2">
                                                <span class="font-mono bg-gray-100 px-1.5 rounded text-gray-500">{{ $item->product_code }}</span>
                                                <span class="text-gray-300">|</span>
                                                <span>{{ $item->category }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $stockColor = $item->stock_quantity <= 5 ? 'red' : ($item->stock_quantity <= 10 ? 'yellow' : 'green');
                                        $bgColor = "bg-{$stockColor}-100";
                                        $textColor = "text-{$stockColor}-700";
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $bgColor }} {{ $textColor }}">
                                        {{ $item->stock_quantity }} Unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm text-gray-500 font-mono">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-gray-900 font-mono">Rp {{ number_format($item->sell_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('warehouse.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada barang.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    {{ $goods->appends(['services_page' => request('services_page')])->links() }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Layanan Jasa</h3>
                            <p class="text-xs text-gray-500">Service & Perawatan</p>
                        </div>
                    </div>
                    <span class="bg-white text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200 shadow-sm">
                        {{ $services->total() }} Layanan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100 bg-white">
                            <th class="px-6 py-4">Nama Layanan</th>
                            <th class="px-6 py-4 text-center">Kategori</th>
                            <th class="px-6 py-4 text-right">Tarif Jasa</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($services as $item)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center font-bold text-lg border border-blue-100">
                                            JS
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm group-hover:text-blue-600 transition">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $item->product_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-blue-600 font-mono">Rp {{ number_format($item->sell_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('warehouse.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada layanan jasa.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    {{ $services->appends(['goods_page' => request('goods_page')])->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
