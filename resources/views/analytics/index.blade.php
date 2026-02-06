<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
            <span>📈</span> {{ __('Laporan & Analitik Bisnis') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                <form method="GET" action="{{ route('analytics') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Grafik</label>
                        <select name="period" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <button type="submit" class="w-full bg-brand-900 hover:bg-black text-white font-bold py-2 px-4 rounded-lg shadow transition text-sm">
                            Filter Laporan
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan (Omset)</div>
                    <div class="mt-2 text-2xl font-black text-brand-900">
                        Rp {{ number_format($summary['revenue'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-green-600 font-bold mt-1">
                        {{ $summary['transactions'] }} Transaksi Berhasil
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Profit Bersih (Estimasi)</div>
                    <div class="mt-2 text-2xl font-black text-green-600">
                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Omset dikurangi Harga Modal</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Gudang (Stock Balance)</div>
                    <div class="mt-2 text-2xl font-black text-blue-600">
                        Rp {{ number_format($stockBalance->total_asset_value ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ number_format($stockBalance->total_stock_qty ?? 0) }} Unit barang belum terjual</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Volume Penjualan</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-orange-600">{{ number_format($summary['goods_sold']) }}</span>
                        <span class="text-xs font-bold text-gray-500">Barang</span>
                    </div>
                    <div class="flex items-baseline gap-2 border-t border-gray-100 pt-1 mt-1">
                        <span class="text-lg font-bold text-blue-600">{{ number_format($summary['services_booked']) }}</span>
                        <span class="text-xs font-bold text-gray-500">Jasa Service</span>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-4">Grafik: Omset vs Profit</h3>
                    <div class="relative h-80 w-full">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-red-100 text-red-600 p-1.5 rounded-md text-xs">⚠️</span>
                        <h3 class="font-bold text-gray-800">Stok Menipis (< 5)</h3>
                    </div>
                    <div class="space-y-3 overflow-y-auto max-h-80 custom-scrollbar pr-2">
                        @forelse($lowStockItems as $item)
                            <div class="flex justify-between items-center pb-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <div class="text-sm font-bold text-gray-700">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->product_code }}</div>
                                </div>
                                <span class="bg-red-50 text-red-600 px-2 py-1 rounded text-xs font-bold">
                                    Sisa {{ $item->stock_quantity }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic text-center py-10">Stok aman terkendali.</p>
                        @endforelse
                    </div>
                    @if($lowStockItems->isNotEmpty())
                        <a href="{{ route('warehouse.index') }}" class="block mt-4 text-center text-xs font-bold text-blue-600 hover:underline">
                            Kelola Stok Gudang →
                        </a>
                    @endif
                </div>

            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Rincian Penjualan Produk & Jasa (Top 10)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Produk / Layanan</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3 text-center">Volume</th>
                            <th class="px-6 py-3 text-right">Total Omset</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($productBreakdown as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-bold text-gray-700">
                                    {{ $item->name }}
                                    @if($item->type == 'service')
                                        <span class="ml-2 bg-blue-100 text-blue-600 text-[10px] px-1.5 py-0.5 rounded">JASA</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $item->product_code }}</td>

                                <td class="px-6 py-3 text-center">
                                        <span class="px-2 py-1 rounded font-bold text-xs {{ $item->type == 'service' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700' }}">
                                            {{ $item->total_qty }}
                                            {{ $item->type == 'service' ? 'Kali' : 'Unit' }}
                                        </span>
                                </td>

                                <td class="px-6 py-3 text-right font-mono font-bold text-brand-700">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data penjualan pada periode ini.</td>
                            </tr>
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

            const labels = @json($chartData['labels']);
            const dataRevenue = @json($chartData['revenue']);
            const dataProfit = @json($chartData['profit']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Omset (Revenue)',
                            data: dataRevenue,
                            borderColor: '#111827', // Brand Black
                            backgroundColor: 'rgba(17, 24, 39, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Profit (Laba)',
                            data: dataProfit,
                            borderColor: '#16a34a', // Green
                            backgroundColor: 'rgba(22, 163, 74, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', align: 'end' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 2], color: '#f3f4f6' },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', notation: "compact" }).format(value);
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
