<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
                <span>🕰️</span> {{ __('Pusat Riwayat & Log') }}
            </h2>
            <p class="text-sm text-gray-500">Lacak jejak barang, penjualan, dan aktivitas stok.</p>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1">
                <form method="GET" action="{{ route('history.index') }}" class="relative flex items-center w-full">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $query }}"
                           class="w-full pl-12 pr-4 py-4 rounded-xl border-none focus:ring-0 text-gray-800 placeholder-gray-400 text-lg"
                           placeholder="Ketik Nomor Invoice (INV-...), Nama Produk, atau Nama Kasir..."
                           autocomplete="off">
                    <div class="pr-2 flex items-center gap-2">
                        @if($query)
                            <a href="{{ route('history.index', ['tab' => $tab]) }}" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition" title="Reset">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                        <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-lg font-bold hover:bg-gray-800 transition shadow-md text-sm">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            @if($query && $searchResults && $searchResults->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-blue-100 overflow-hidden">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                        <h3 class="font-bold text-blue-900">🔍 Hasil Pencarian Transaksi ({{ $searchResults->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-white text-gray-500 font-bold border-b">
                            <tr>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                            @foreach($searchResults as $trx)
                                <tr class="hover:bg-blue-50/50">
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $trx->created_at->timezone('Asia/Jakarta')->format('d M H:i') }}</td>
                                    <td class="px-6 py-4 font-bold">{{ $trx->invoice_code }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">{{ strtoupper($trx->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="border-b border-gray-100 px-6 pt-6 pb-0">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-900">Log Pergerakan Stok</h3>
                    </div>

                    <div class="flex gap-8">
                        <a href="{{ route('history.index', ['tab' => 'all', 'search' => $query]) }}"
                           class="pb-3 border-b-2 text-sm font-medium transition {{ $tab == 'all' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Semua Mutasi
                        </a>

                        <a href="{{ route('history.index', ['tab' => 'in', 'search' => $query]) }}"
                           class="pb-3 border-b-2 text-sm font-medium transition {{ $tab == 'in' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-green-600 hover:border-green-200' }}">
                            ⬇️ Barang Masuk
                        </a>

                        <a href="{{ route('history.index', ['tab' => 'out', 'search' => $query]) }}"
                           class="pb-3 border-b-2 text-sm font-medium transition {{ $tab == 'out' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-red-600 hover:border-red-200' }}">
                            ⬆️ Barang Keluar
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-6 py-4">Waktu (WIB)</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Tipe</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">User</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @forelse($stockLogs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-500 font-mono text-xs whitespace-nowrap">
                                    {{ $log->created_at->timezone('Asia/Jakarta')->format('d M H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $log->product->name ?? 'Item Dihapus' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($log->type == 'in')
                                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-100">
                                                MASUK
                                            </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 px-2 py-1 rounded text-[10px] font-bold border border-red-100">
                                                KELUAR
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900">
                                    {{ $log->quantity }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs font-mono max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                    Tidak ada data untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $stockLogs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
