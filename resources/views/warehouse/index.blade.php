<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-brand-900 leading-tight whitespace-nowrap">
                {{ __('Manajemen Gudang') }}
            </h2>

            <div class="flex w-full md:w-auto gap-3">
                <form method="GET" action="{{ route('warehouse.index') }}" class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari Barang / Jasa..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition text-sm shadow-sm">
                </form>

                <a href="{{ route('warehouse.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition flex items-center text-sm whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Produk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Jika sedang mencari, tampilkan info --}}
            @if(request('search'))
                <div class="bg-brand-50 border border-brand-200 text-brand-700 px-4 py-3 rounded-lg flex justify-between items-center">
                    <div>
                        <span class="font-bold">Hasil pencarian:</span> "{{ request('search') }}"
                    </div>
                    <a href="{{ route('warehouse.index') }}" class="text-xs font-bold underline hover:text-brand-900">Reset Pencarian</a>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-brand-900 flex items-center">
                            <span class="bg-brand-100 text-brand-600 p-2 rounded-lg mr-3">📦</span>
                            Stok Barang (Sparepart)
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 ml-12">Produk fisik dengan manajemen stok & modal.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Beli (Modal)</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @php $hasGoods = false; @endphp
                        @foreach($products as $product)
                            @if($product->type === 'goods' || $product->type === null)
                                @php $hasGoods = true; @endphp
                                <tr class="hover:bg-orange-50/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center text-xl border border-gray-200">📦</div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $product->product_code }} • {{ $product->category ?? 'Umum' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($product->stock_quantity <= 5)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 animate-pulse">
                                                    {{ $product->stock_quantity }} Unit
                                                </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                                    {{ $product->stock_quantity }} Unit
                                                </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        Rp {{ number_format($product->buy_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-brand-700">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @hasanyrole('Owner|Admin')
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('warehouse.edit', $product->id) }}" class="text-gray-400 hover:text-brand-600 transition p-1" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>

                                            <form action="{{ route('warehouse.destroy', $product->id) }}" method="POST" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete text-gray-400 hover:text-red-600 transition p-1" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic flex items-center justify-center">Locked</span>
                                            @endhasanyrole
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if(!$hasGoods)
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                    {{ request('search') ? 'Barang tidak ditemukan.' : 'Belum ada data barang.' }}
                                </td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-blue-900 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">🔧</span>
                            Daftar Jasa Service
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 ml-12">Layanan jasa bengkel (Non-Stok).</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Layanan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Biaya / Tarif</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @php $hasService = false; @endphp
                        @foreach($products as $product)
                            @if($product->type === 'service')
                                @php $hasService = true; @endphp
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center text-xl border border-blue-100 text-blue-500">🔧</div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                                <div class="text-xs text-blue-400">{{ $product->product_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 inline-flex text-[10px] leading-5 font-bold rounded bg-gray-100 text-gray-600 border border-gray-200">∞ UNLIMITED</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @hasanyrole('Owner|Admin')
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('warehouse.edit', $product->id) }}" class="text-gray-400 hover:text-blue-600 transition p-1" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>

                                            <form action="{{ route('warehouse.destroy', $product->id) }}" method="POST" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete text-gray-400 hover:text-red-600 transition p-1" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic flex items-center justify-center">Locked</span>
                                            @endhasanyrole
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if(!$hasService)
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                    {{ request('search') ? 'Jasa tidak ditemukan.' : 'Belum ada data jasa.' }}
                                </td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $products->withQueryString()->links() }}
            </div>

        </div>
    </div>

    {{-- Script SweetAlert --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    let form = this.closest('form');
                    Swal.fire({
                        title: 'Hapus Produk Ini?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
</x-app-layout>
