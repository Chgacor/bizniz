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
                        <select name="period" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black cursor-pointer bg-gray-50">
                            <option value="hourly" {{ $period == 'hourly' ? 'selected' : '' }}>⏱️ Per Jam (00-23)</option>
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>📅 Harian (Rentang)</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>🗓️ Mingguan</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>📊 Bulanan (Jan-Des)</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>📅 Tahunan (5 Thn)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                            {{ $period == 'hourly' ? 'Pilih Tanggal' : ($period == 'yearly' ? 'Mulai Tahun' : 'Dari Tanggal') }}
                        </label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black">
                    </div>

                    <div class="{{ ($period != 'daily') ? 'opacity-40 pointer-events-none' : '' }}">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-black" {{ ($period != 'daily') ? 'readonly' : '' }}>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-gray-300 transition">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan (Omset)</div>
                    <div class="mt-2 text-2xl font-black text-gray-900">
                        Rp {{ number_format($summary['revenue'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-green-600 font-bold mt-1 bg-green-50 inline-block px-1.5 py-0.5 rounded">
                        {{ $summary['transactions'] }} Transaksi Berhasil
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-gray-300 transition">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Profit Bersih (Estimasi)</div>
                    <div class="mt-2 text-2xl font-black text-green-600">
                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Omset dikurangi Harga Modal</div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-gray-300 transition">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Gudang (Stock Balance)</div>
                    <div class="mt-2 text-2xl font-black text-blue-600">
                        Rp {{ number_format($stockBalance->total_asset_value ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        {{ number_format($stockBalance->total_stock_qty ?? 0) }} Unit barang belum terjual
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-gray-300 transition">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Volume Penjualan</div>
                    <div class="mt-2 flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-orange-600">{{ $summary['goods_sold'] }}</span>
                            <span class="text-xs text-gray-500">Barang</span>
                        </div>
                        <div class="w-full h-px bg-gray-100"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-blue-600">{{ $summary['services_booked'] }}</span>
                            <span class="text-xs text-gray-500">Jasa Service</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 text-lg">Grafik: Omset vs Profit</h3>

                        <div class="flex gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1.5 border border-gray-200 px-2 py-1 rounded bg-gray-50">
                                <span class="w-3 h-3 rounded-sm border border-gray-900 bg-gray-900"></span>
                                Omset (Revenue)
                            </div>
                            <div class="flex items-center gap-1.5 border border-green-100 px-2 py-1 rounded bg-green-50">
                                <span class="w-3 h-3 rounded-sm border border-green-500 bg-white"></span>
                                Profit (Laba)
                            </div>
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
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Rincian Penjualan Produk & Jasa (Top 10)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white text-gray-500 font-bold border-b border-gray-100 uppercase tracking-wider text-xs">
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
                                        <span class="ml-2 bg-blue-100 text-blue-600 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">JASA</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $item->product_code }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2 py-1 rounded font-bold text-xs {{ $item->type == 'service' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700' }}">
                                        {{ $item->total_qty }}
                                        {{ $item->type == 'service' ? 'x' : 'pc' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data penjualan pada periode ini.</td>
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
                            // STYLE AREA CHART (HITAM) - MULUS
                            borderColor: '#111827', // Hitam Pekat
                            backgroundColor: 'rgba(17, 24, 39, 0.15)', // Hitam Transparan
                            borderWidth: 2,
                            tension: 0.4, // Kurva Mulus (Spline)
                            fill: true,   // Isi Area Bawah
                            pointRadius: 3, // Titik kecil
                            pointHoverRadius: 6, // Titik besar saat hover
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#111827',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Profit (Laba)',
                            data: dataProfit,
                            // STYLE AREA CHART (HIJAU) - MULUS
                            borderColor: '#16a34a', // Hijau
                            backgroundColor: 'rgba(22, 163, 74, 0.05)', // Hijau Transparan Tipis
                            borderWidth: 2,
                            tension: 0.4, // Kurva Mulus
                            fill: true,   // Isi Area Bawah
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#16a34a',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }, // Legend kita buat sendiri di HTML
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 12,
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 13, family: "'Inter', sans-serif" },
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: {
                                font: { size: 11 },
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', notation: "compact", maximumFractionDigits: 1 }).format(value);
                                }
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#6b7280' },
                            border: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
