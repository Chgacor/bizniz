<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">

            {{-- HEADER ATAS: Judul & Tombol Aksi --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-200 pb-6">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ __('Gudang & Logistik') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Pusat kontrol stok, pembelian, dan retur barang.</p>
                </div>

                {{-- TOMBOL AKSI CEPAT --}}
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

            {{-- BARIS KEDUA: Search & Main Actions --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4" x-data>

                {{-- SEARCH BAR --}}
                <form method="GET" action="{{ route('warehouse.index') }}" class="relative w-full md:w-80 group">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari barang / kode..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition text-sm shadow-sm placeholder-gray-400 font-bold">
                </form>

                {{-- ACTION BUTTONS (EXCEL & ADD) --}}
                <div class="flex gap-2 w-full md:w-auto">
                    {{-- TOMBOL EXPORT EXCEL --}}
                    <a href="{{ route('warehouse.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl shadow transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Excel
                    </a>

                    {{-- TOMBOL TAMBAH ITEM --}}
                    <a href="{{ route('warehouse.create') }}" class="bg-brand-900 hover:bg-black text-white font-bold py-2.5 px-6 rounded-xl shadow transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Item
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen" x-data="{ tab: 'goods' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 px-6 py-4 rounded-xl shadow-sm border border-green-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- TAB SWITCHER --}}
            <div class="bg-white p-1.5 rounded-xl shadow-sm border border-gray-200 inline-flex w-full md:w-auto">
                <button @click="tab = 'goods'"
                        :class="tab === 'goods' ? 'bg-orange-100 text-orange-800 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition flex items-center justify-center gap-2">
                    <span>📦</span> Data Barang
                </button>
                <button @click="tab = 'services'"
                        :class="tab === 'services' ? 'bg-blue-100 text-blue-800 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition flex items-center justify-center gap-2">
                    <span>🔧</span> Data Jasa
                </button>
            </div>

            {{-- TAB 1: BARANG (GOODS) --}}
            <div x-show="tab === 'goods'" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-500 tracking-wider text-left">
                        <tr>
                            <th class="px-6 py-4">Produk Info</th>
                            <th class="px-6 py-4 text-center">Stok Gudang</th>
                            <th class="px-6 py-4 text-right">Modal (HPP)</th>
                            <th class="px-6 py-4 text-right">Harga Jual (HET)</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($goods as $item)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-xl overflow-hidden">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" class="h-full w-full object-cover">
                                            @else
                                                📦
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono font-bold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border">{{ $item->product_code }}</span>
                                                <span class="text-[10px] uppercase font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded">{{ $item->category }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->stock_quantity <= 5)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 animate-pulse">
                                                {{ $item->stock_quantity }} Unit (Menipis)
                                            </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                                {{ $item->stock_quantity }} Unit
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-500 font-mono">
                                    Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-brand-900 font-mono">
                                    Rp {{ number_format($item->sell_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('warehouse.edit', $item->id) }}" class="text-gray-400 hover:text-blue-600 transition p-1 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 bg-gray-50 rounded-lg border border-gray-200 hover:border-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50 flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    Belum ada barang di kategori ini.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $goods->appends(['services_page' => request('services_page')])->links() }}
                </div>
            </div>

            {{-- TAB 2: JASA (SERVICES) --}}
            <div x-show="tab === 'services'" style="display: none;" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-500 tracking-wider text-left">
                        <tr>
                            <th class="px-6 py-4">Nama Layanan</th>
                            <th class="px-6 py-4 text-center">Kategori</th>
                            <th class="px-6 py-4 text-right">Tarif Jasa</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($services as $item)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-lg">
                                            🔧
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 inline-flex text-xs font-bold rounded bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $item->category }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-brand-900 font-mono">
                                    Rp {{ number_format($item->sell_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('warehouse.edit', $item->id) }}" class="text-gray-400 hover:text-blue-600 transition p-1 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus jasa ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 bg-gray-50 rounded-lg border border-gray-200 hover:border-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic bg-gray-50">
                                    Belum ada data jasa service.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $services->appends(['goods_page' => request('goods_page')])->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>