<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Riwayat Retur Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-700 text-lg">Log Pengembalian Barang</h3>
                        <a href="{{ route('warehouse.index') }}" class="text-brand-600 hover:text-brand-800 font-bold text-sm">
                            &larr; Kembali ke Gudang
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200 uppercase text-gray-500 font-bold text-xs">
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
                                    <td class="px-6 py-4">{{ $ret->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-mono font-bold text-red-600">{{ $ret->return_code }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $ret->transaction->invoice_code ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ Str::limit($ret->reason, 30) }}</td>
                                    <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($ret->total_refund, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ $ret->user->name }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data retur.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
