<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Analitik & Laporan Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div>
                <h3 class="font-bold text-gray-700 mb-4 flex items-center text-lg">
                    <span class="bg-brand-900 text-white p-1 rounded mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Performa Keuangan Bulan Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omzet (Revenue)</div>
                        <div class="text-3xl font-extrabold text-gray-800 mt-2">
                            Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-red-500">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Modal (HPP)</div>
                        <div class="text-3xl font-extrabold text-red-600 mt-2">
                            - Rp {{ number_format($monthCogs, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500 relative overflow-hidden">
                        <div class="absolute right-0 top-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laba Kotor (Gross Profit)</div>
                        <div class="text-3xl font-extrabold text-green-600 mt-2">
                            Rp {{ number_format($monthProfit, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Laporan Penjualan (CSV)
                </h3>
                <form action="{{ route('reports.export') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="w-full rounded border-gray-300" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="w-full rounded border-gray-300" required value="{{ date('Y-m-d') }}">
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded transition h-10">
                        Download Laporan
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-brand-50">
                        <h3 class="font-bold text-brand-900">🏆 Produk Terlaris</h3>
                    </div>
                    <div class="p-4">
                        <ul class="divide-y divide-gray-100">
                            @forelse($topProducts as $item)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="bg-brand-100 text-brand-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                        <span class="font-medium text-gray-800 text-sm">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                    </div>
                                    <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded">{{ $item->total_sold }} Terjual</span>
                                </li>
                            @empty
                                <li class="py-3 text-center text-gray-400">Belum ada data.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-brand-50">
                        <h3 class="font-bold text-brand-900">⏱️ Transaksi Terakhir</h3>
                    </div>
                    <div class="p-4">
                        <ul class="divide-y divide-gray-100">
                            @forelse($recentTransactions as $txn)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $txn->invoice_code }}</div>
                                        <div class="text-xs text-gray-500">{{ $txn->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-green-600 text-sm">+ Rp {{ number_format($txn->total_amount, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-400">{{ $txn->user->name ?? 'Staff' }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-3 text-center text-gray-400">Belum ada transaksi hari ini.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
