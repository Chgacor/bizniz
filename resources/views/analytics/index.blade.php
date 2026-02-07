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
                        <select name="period" id="periodSelect" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black focus:border-black cursor-pointer bg-gray-50">
                            <option value="hourly" {{ $period == 'hourly' ? 'selected' : '' }}>⏱️ Per Jam (1 Hari Full)</option>
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>📅 Harian</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>🗓️ Mingguan</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>📊 Bulanan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                            {{ $period == 'hourly' ? 'Pilih Tanggal' : 'Dari Tanggal' }}
                        </label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black focus:border-black">
                    </div>

                    <div class="{{ $period == 'hourly' ? 'opacity-50 pointer-events-none' : '' }}">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black focus:border-black" {{ $period == 'hourly' ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Tampilkan Data
                        </button>
                    </div>
                </form>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('analytics.export') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">

                        <button type="submit" class="text-xs font-bold text-green-700 hover:text-green-900 flex items-center gap-1 group bg-green-50 px-3 py-2 rounded-lg hover:bg-green-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Laporan CSV (.csv)
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omset</div>
                    <div class="mt-2 text-2xl font-black text-brand-900">
                        Rp {{ number_format($summary['revenue'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-green-600 font-bold mt-1">{{ $summary['transactions'] }} Transaksi</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Modal (HPP)</div>
                    <div class="mt-2 text-2xl font-black text-gray-700">
                        Rp {{ number_format($summary['cost'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Estimasi Modal Barang</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Profit Bersih</div>
                    <div class="mt-2 text-2xl font-black text-green-600">
                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Omset - Modal</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Gudang</div>
                    <div class="mt-2 text-2xl font-black text-blue-600">
                        Rp {{ number_format($stockBalance->total_asset_value ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ number_format($stockBalance->total_stock_qty ?? 0) }} Unit Stok</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800">
                            Grafik: Omset vs Profit
                            <span class="text-xs font-normal text-gray-500 ml-2">({{ $period == 'hourly' ? 'Per Jam' : ucfirst($period) }})</span>
                        </h3>
                        <div class="flex gap-4 text-xs">
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-800"></span> Omset</div>
                            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Profit</div>
                        </div>
                    </div>
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
                                <span class="bg-red-50 text-red-600 px-2 py-1 rounded text-xs font-bold border border-red-100">Sisa {{ $item->stock_quantity }}</span>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-10 text-sm">Stok aman.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Produk Terlaris (Top 10)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Produk</th>
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
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2 py-1 rounded font-bold text-xs {{ $item->type == 'service' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700' }}">
                                        {{ $item->total_qty }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-gray-400">Tidak ada data.</td></tr>
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
                            label: 'Omset',
                            data: dataRevenue,
                            // STYLE AREA CHART (HITAM)
                            borderColor: '#111827',
                            backgroundColor: 'rgba(17, 24, 39, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Profit',
                            data: dataProfit,
                            // STYLE AREA CHART (HIJAU)
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
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
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', notation: "compact", maximumFractionDigits: 1 }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
