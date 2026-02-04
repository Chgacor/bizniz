<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Pusat Riwayat & Log') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="{ activeTab: 'movements' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1 flex overflow-x-auto">
                <button @click="activeTab = 'movements'"
                        :class="activeTab === 'movements' ? 'bg-brand-900 text-white shadow' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex-1 py-2.5 px-4 rounded-lg font-bold text-sm transition flex items-center justify-center gap-2 whitespace-nowrap">
                    📊 Mutasi Stok
                </button>
                <button @click="activeTab = 'purchases'"
                        :class="activeTab === 'purchases' ? 'bg-brand-900 text-white shadow' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex-1 py-2.5 px-4 rounded-lg font-bold text-sm transition flex items-center justify-center gap-2 whitespace-nowrap">
                    ⬇️ Stok Masuk (Beli)
                </button>
                <button @click="activeTab = 'returns'"
                        :class="activeTab === 'returns' ? 'bg-brand-900 text-white shadow' : 'text-gray-600 hover:bg-gray-50'"
                        class="flex-1 py-2.5 px-4 rounded-lg font-bold text-sm transition flex items-center justify-center gap-2 whitespace-nowrap">
                    🔄 Retur Penjualan
                </button>
            </div>

            <div x-show="activeTab === 'movements'" x-transition.opacity>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800">Log Pergerakan Stok (Live)</h3>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-bold text-xs">
                        <tr>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3 text-center">Tipe</th>
                            <th class="px-6 py-3 text-center">Jumlah</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3">User</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($movements as $mov)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500 text-xs">{{ $mov->created_at->format('d M H:i') }}</td>
                                <td class="px-6 py-3 font-bold text-gray-700">{{ $mov->product->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($mov->type == 'in' || $mov->type == 'adjustment_in')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">MASUK</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">KELUAR</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center font-bold">{{ $mov->quantity }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $mov->description }}</td>
                                <td class="px-6 py-3 text-xs text-gray-400">{{ $mov->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada aktivitas stok.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-100">
                        {{ $movements->appends(['purchases_page' => request('purchases_page'), 'returns_page' => request('returns_page')])->links() }}
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'purchases'" style="display: none;" x-transition.opacity>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Riwayat Stok Masuk</h3>
                        <a href="{{ route('purchase.create') }}" class="text-xs bg-black text-white px-3 py-1.5 rounded font-bold hover:bg-gray-800">+ Input Baru</a>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-bold text-xs">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">No. Invoice</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3 text-right">Total Belanja</th>
                            <th class="px-6 py-3">Input Oleh</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($purchases as $inv)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $inv->invoice_date }}</td>
                                <td class="px-6 py-3 font-mono font-bold text-brand-600">{{ $inv->invoice_number }}</td>
                                <td class="px-6 py-3">{{ $inv->supplier_name }}</td>
                                <td class="px-6 py-3 text-right font-bold">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $inv->user->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data pembelian.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-100">
                        {{ $purchases->appends(['movements_page' => request('movements_page'), 'returns_page' => request('returns_page')])->links() }}
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'returns'" style="display: none;" x-transition.opacity>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Riwayat Retur Penjualan</h3>
                        <a href="{{ route('returns.create') }}" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded font-bold hover:bg-red-700">+ Proses Retur</a>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-bold text-xs">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Kode Retur</th>
                            <th class="px-6 py-3">Ex. Invoice</th>
                            <th class="px-6 py-3">Alasan</th>
                            <th class="px-6 py-3 text-right">Refund</th>
                            <th class="px-6 py-3">Diproses Oleh</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($returns as $ret)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $ret->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 font-mono font-bold text-red-600">{{ $ret->return_code }}</td>
                                <td class="px-6 py-3 font-mono text-gray-500">{{ $ret->transaction->invoice_code ?? '-' }}</td>
                                <td class="px-6 py-3">{{ Str::limit($ret->reason, 40) }}</td>
                                <td class="px-6 py-3 text-right font-bold">Rp {{ number_format($ret->total_refund, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $ret->user->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data retur.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-100">
                        {{ $returns->appends(['movements_page' => request('movements_page'), 'purchases_page' => request('purchases_page')])->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
