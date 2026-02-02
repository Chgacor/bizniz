<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Business Intelligence & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <form method="GET" action="{{ route('analytics') }}" class="flex flex-col md:flex-row gap-4 items-end">

                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grup Berdasarkan</label>
                        <select name="period" class="w-full rounded-lg border-gray-300">
                            <option value="hourly" {{ $period == 'hourly' ? 'selected' : '' }}>Per Jam (Hourly)</option>
                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Per Hari (Daily)</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Per Minggu (Weekly)</option>
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Per Bulan (Monthly)</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Per Tahun (Yearly)</option>
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <button type="submit" class="w-full bg-brand-900 hover:bg-black text-white font-bold py-2.5 px-4 rounded-lg shadow transition">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-brand-500 rounded-xl p-6 text-white shadow-md">
                    <div class="text-brand-100 text-sm font-bold uppercase opacity-90">Total Omset (Range Ini)</div>
                    <div class="text-3xl font-extrabold mt-2">
                        Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="text-gray-500 text-sm font-bold uppercase">Total Transaksi</div>
                    <div class="text-3xl font-extrabold text-brand-900 mt-2">
                        {{ $summary['total_trx'] }}
                        <span class="text-sm font-normal text-gray-400">Nota</span>
                    </div>
                </div>

            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Penjualan: Barang vs Jasa</h3>
                <div class="relative h-80 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-orange-100 text-orange-600 p-2 rounded-lg mr-3">📦</span>
                        Top 5 Barang Terlaris
                    </h3>
                    <div class="space-y-4">
                        @foreach($topGoods as $index => $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="font-bold text-gray-400 w-6">#{{ $index + 1 }}</span>
                                    <span class="font-semibold text-gray-700">{{ $item->name }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-brand-900">{{ $item->total_qty }} Unit</div>
                                    <div class="text-xs text-gray-500">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                        @if($topGoods->isEmpty())
                            <p class="text-center text-gray-400 py-4">Belum ada data barang.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">🛠️</span>
                        Top 5 Jasa Sering Dipakai
                    </h3>
                    <div class="space-y-4">
                        @foreach($topServices as $index => $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="font-bold text-gray-400 w-6">#{{ $index + 1 }}</span>
                                    <span class="font-semibold text-gray-700">{{ $item->name }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-blue-600">{{ $item->total_qty }}x</div>
                                    <div class="text-xs text-gray-500">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                        @if($topServices->isEmpty())
                            <p class="text-center text-gray-400 py-4">Belum ada data jasa.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');

            const labels = @json($chartData['labels']);
            const dataGoods = @json($chartData['goods']);
            const dataServices = @json($chartData['services']);

            // Warna Pastel Simpel (Sesuai tema Soft Orange Anda)
            const goodsColorBg = '#fdba74';
            const goodsColorBorder = '#fb923c';

            const servicesColorBg = '#93c5fd';
            const servicesColorBorder = '#60a5fa';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Barang (Goods)',
                            data: dataGoods,
                            backgroundColor: goodsColorBg,
                            borderColor: goodsColorBorder,
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Jasa (Services)',
                            data: dataServices,
                            backgroundColor: servicesColorBg,
                            borderColor: servicesColorBorder,
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                font: { family: "'Inter', sans-serif", size: 12 },
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Tooltip tetap detail (Rp 1.500.000)
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            // FIX 1: Setel plafon minimal 1 Juta agar grafik tidak terlihat "receh" saat data kosong
                            suggestedMax: 1000000,

                            grid: { color: '#f5f5f4' },

                            ticks: {
                                font: { family: "'Inter', sans-serif" },
                                // FIX 2: Paksa angka bulat (Hapus desimal 0,1 0,9)
                                precision: 0,

                                callback: function(value) {
                                    // FIX 3: Format Rupiah "Pintar" (Auto Scale)
                                    // 100.000 -> 100 Rb
                                    // 10.000.000 -> 10 Jt
                                    // 1.500.000.000 -> 1,5 M
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        notation: "compact", // Ini kuncinya!
                                        maximumFractionDigits: 1
                                    }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Inter', sans-serif" } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
