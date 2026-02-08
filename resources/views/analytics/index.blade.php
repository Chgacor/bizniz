<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
            <span>📈</span> {{ __('Laporan & Analitik Bisnis') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <form method="GET" action="{{ route('analytics') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe Laporan</label>
                        <select name="period" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black">
                            <option value="hourly" {{ $period == 'hourly' ? 'selected' : '' }}>⏱️ Per Jam (00-23)</option>
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>📅 Harian (Rentang)</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>📊 Bulanan (Jan-Des)</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>📅 Tahunan (5 Tahun)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                            {{ $period == 'hourly' ? 'Pilih Tanggal' : ($period == 'yearly' ? 'Mulai Tahun' : 'Dari Tanggal') }}
                        </label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black">
                    </div>
                    <div class="{{ ($period != 'daily') ? 'opacity-30 pointer-events-none' : '' }}">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black" {{ ($period != 'daily') ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-2.5 px-4 rounded-lg shadow text-sm">Tampilkan Data</button>
                    </div>
                </form>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('analytics.export') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="w-full sm:w-auto text-xs font-bold text-green-700 bg-green-50 px-4 py-3 rounded-lg hover:bg-green-100 border border-green-200 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download Laporan Excel (.xls)
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omset</div>
                    <div class="mt-2 text-2xl font-black text-brand-900">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
                    <div class="text-xs text-green-600 font-bold mt-1">{{ $summary['transactions'] }} Transaksi</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Modal Terjual (HPP)</div>
                    <div class="mt-2 text-2xl font-black text-gray-700">Rp {{ number_format($summary['cost'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Modal dari barang laku</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Profit Bersih</div>
                    <div class="mt-2 text-2xl font-black text-green-600">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Omset - Modal</div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-blue-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-10">
                        <svg class="w-16 h-16 text-blue-900" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" /><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-3">Nilai Aset Gudang (Live)</div>
                    <div class="mb-2">
                        <div class="text-[10px] text-gray-500 font-bold uppercase">Harga Modal (HPP)</div>
                        <div class="text-xl font-black text-blue-700">Rp {{ number_format($stockBalance->total_asset_hpp ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="border-t border-blue-50 pt-2">
                        <div class="text-[10px] text-gray-500 font-bold uppercase">Potensi Jual (HET)</div>
                        <div class="text-lg font-bold text-gray-600">Rp {{ number_format($stockBalance->total_asset_het ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-2 text-right">{{ number_format($stockBalance->total_stock_qty ?? 0) }} Unit Stok Fisik</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800">Visualisasi Bisnis <span class="text-xs font-normal text-gray-400">({{ ucfirst($period) }})</span></h3>
                        <div class="flex gap-4 text-xs font-bold">
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-800"></span> Omset</div>
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Profit</div>
                        </div>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-red-50">
                        <span class="bg-red-100 text-red-600 p-1.5 rounded-md text-xs">⚠️</span>
                        <h3 class="font-bold text-gray-800">Stok Menipis (< 5)</h3>
                    </div>

                    <div class="space-y-3 overflow-y-auto max-h-80 pr-1 custom-scrollbar">
                        @forelse($lowStockItems as $item)
                            <div class="flex justify-between items-start pb-2 border-b border-gray-50 last:border-0 hover:bg-gray-50 p-2 rounded transition">
                                <div>
                                    <div class="text-sm font-bold text-gray-800">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $item->product_code }}</div>
                                </div>
                                <span class="bg-red-50 text-red-600 px-2 py-1 rounded text-xs font-bold border border-red-100 whitespace-nowrap">
                                    Sisa {{ $item->stock_quantity }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <span class="text-2xl">👌</span>
                                <p class="text-sm text-gray-400 mt-2">Stok aman terkendali.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($lowStockItems->isNotEmpty())
                        <a href="{{ route('warehouse.index') }}" class="block mt-4 text-center text-xs font-bold text-blue-600 hover:underline hover:text-blue-800 bg-blue-50 py-2 rounded-lg transition">
                            Kelola Stok Gudang →
                        </a>
                    @endif
                </div>

            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Produk Terlaris</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white font-bold border-b">
                        <tr>
                            <th class="px-6 py-3">Produk / Layanan</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3 text-center">Volume</th>
                            <th class="px-6 py-3 text-right">Omset</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($productBreakdown as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-bold text-gray-700">
                                    {{ $item->name }}
                                    @if($item->type == 'service') <span class="ml-1 text-[10px] bg-blue-100 text-blue-600 px-1 rounded">JASA</span> @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs">{{ $item->product_code }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->total_qty }}</td>
                                <td class="px-6 py-3 text-right font-mono text-gray-900">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-6 text-gray-400">Tidak ada data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('profitChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Omset', data: @json($chartData['revenue']),
                            borderColor: '#111827', backgroundColor: 'rgba(17, 24, 39, 0.1)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 6
                        },
                        {
                            label: 'Profit', data: @json($chartData['profit']),
                            borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
                }
            });
        });
    </script>
</x-app-layout>
