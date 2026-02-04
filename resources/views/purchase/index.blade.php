<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Riwayat Pembelian (Stok Masuk)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-700 text-lg">Data Invoice Masuk</h3>
                        <a href="{{ route('warehouse.index') }}" class="text-brand-600 hover:text-brand-800 font-bold text-sm">
                            &larr; Kembali ke Gudang
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200 uppercase text-gray-500 font-bold text-xs">
                            <tr>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">No. Invoice</th>
                                <th class="px-6 py-3">Supplier</th>
                                <th class="px-6 py-3 text-right">Total Belanja</th>
                                <th class="px-6 py-3">Input Oleh</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $inv->invoice_date }}</td>
                                    <td class="px-6 py-4 font-mono font-bold text-brand-600">{{ $inv->invoice_number }}</td>
                                    <td class="px-6 py-4">{{ $inv->supplier_name }}</td>
                                    <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ $inv->user->name }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data pembelian.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
