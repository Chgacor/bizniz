<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Keuangan & Kalkulator HPP') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div>
                <h3 class="font-bold text-lg text-gray-700 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Laporan Bulan Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-bold uppercase">Total Omzet</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">
                            Rp {{ number_format($revenue, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-red-500">
                        <div class="text-gray-500 text-sm font-bold uppercase">Total HPP / Modal</div>
                        <div class="text-3xl font-bold text-red-600 mt-2">
                            - Rp {{ number_format($cogs, 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">*Estimasi modal barang terjual</p>
                    </div>

                    <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-bold uppercase">Laba Kotor</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg border border-brand-200" x-data="hppCalculator()">
                <div class="px-6 py-4 border-b border-gray-100 bg-brand-900 text-white flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">🧮 Kalkulator Penetapan Harga</h3>
                        <p class="text-sm text-brand-200">Hitung harga jual yang tepat agar margin keuntungan terjaga.</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Modal (Beli)</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" x-model.number="buyPrice" class="block w-full rounded-md border-gray-300 pl-10 focus:border-brand-500 focus:ring-brand-500 sm:text-sm h-10" placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Margin (%)</label>
                            <input type="number" x-model.number="marginPercent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 h-10 px-3" placeholder="Contoh: 30">
                            <p class="text-xs text-gray-500 mt-1">Berapa % keuntungan yang diinginkan?</p>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-medium text-gray-700">Biaya Lain (Opsional)</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" x-model.number="otherCost" class="block w-full rounded-md border-gray-300 pl-10 focus:border-brand-500 focus:ring-brand-500 sm:text-sm h-10" placeholder="Ongkir, Packing, dll">
                            </div>
                        </div>
                    </div>

                    <div class="bg-brand-50 p-6 rounded-lg border border-brand-100 flex flex-col justify-center text-center md:text-left">
                        <div class="mb-6">
                            <span class="text-gray-500 text-sm uppercase font-bold tracking-wider">Rekomendasi Harga Jual</span>
                            <div class="text-4xl font-extrabold text-brand-700 mt-1 tracking-tight" x-text="formatRupiah(sellPrice)"></div>
                        </div>

                        <div class="flex justify-between border-t border-brand-200 pt-4 items-center">
                            <span class="text-gray-600 font-medium">Profit per Unit:</span>
                            <span class="font-bold text-green-600 text-xl" x-text="formatRupiah(profit)"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('hppCalculator', () => ({
                buyPrice: 0,
                marginPercent: 20, // Default margin 20%
                otherCost: 0,

                // Rumus Hitung Harga Jual
                get sellPrice() {
                    let cost = (this.buyPrice || 0) + (this.otherCost || 0);
                    if (cost <= 0) return 0;
                    // Rumus: Modal + (Modal * Persen / 100)
                    return cost + (cost * (this.marginPercent / 100));
                },

                // Rumus Hitung Profit
                get profit() {
                    let cost = (this.buyPrice || 0) + (this.otherCost || 0);
                    return this.sellPrice - cost;
                },

                // Format ke Rupiah
                formatRupiah(number) {
                    if (isNaN(number) || number === 0) return 'Rp 0';
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }).format(number);
                }
            }));
        });
    </script>
</x-app-layout>
