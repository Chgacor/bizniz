<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    👋 Halo, {{ Auth::user()->name }}!
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang di Pusat Kontrol Bizniz.IO</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                <p class="text-xs text-brand-600 font-bold uppercase tracking-wider">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. SYSTEM ALERTS (Notifikasi & Peringatan) --}}
            @if(auth()->user()->unreadNotifications->count() > 0 || ($lowStockCount ?? 0) > 0)
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 p-2 rounded-full text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-red-800">Perhatian Dibutuhkan!</h3>
                            <p class="text-xs text-red-600 mt-0.5">
                                @if(($lowStockCount ?? 0) > 0) Ada {{ $lowStockCount }} barang yang stoknya menipis. @endif
                                Anda memiliki {{ auth()->user()->unreadNotifications->count() }} notifikasi belum dibaca.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('warehouse.index') }}" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold rounded-lg border border-red-200 transition">
                        Cek Gudang
                    </a>
                </div>
            @endif

            {{-- 2. KPI CARDS (Ringkasan Hari Ini) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-brand-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Omset Hari Ini</p>
                        <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-green-50 text-green-600 p-4 rounded-xl group-hover:bg-green-100 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-blue-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $todayTransactions ?? 0 }} <span class="text-sm font-bold text-gray-400">Nota</span></h3>
                    </div>
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-xl group-hover:bg-blue-100 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between group hover:border-orange-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Peringatan Stok</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $lowStockCount ?? 0 }} <span class="text-sm font-bold text-gray-400">Item Menipis</span></h3>
                    </div>
                    <div class="bg-orange-50 text-orange-600 p-4 rounded-xl group-hover:bg-orange-100 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- 3. TABEL RIWAYAT TERAKHIR (Kolom Kiri - Lebih Lebar) --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span>⏱️</span> 5 Transaksi Terakhir
                        </h3>
                        <a href="{{ route('history.index') }}" class="text-sm font-bold text-brand-600 hover:text-brand-800">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Total (Rp)</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                            @forelse($recentTransactions ?? [] as $trx)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $trx->invoice_code }}</div>
                                        <div class="text-xs text-gray-500">Kasir: {{ $trx->user->name ?? 'Sistem' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $trx->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-brand-900 text-right">
                                        {{ number_format($trx->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400 italic">
                                        Belum ada transaksi hari ini.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4. QUICK ACTIONS (Kolom Kanan) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span>🚀</span> Aksi Cepat
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-4">
                        @role('Staff|Owner|Admin')
                        <a href="{{ route('pos.index') }}" class="flex items-center p-4 bg-brand-500 text-white rounded-xl hover:bg-black transition group shadow-md hover:shadow-lg">
                            <div class="bg-white/20 p-3 rounded-lg mr-4 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">Buka POS Kasir</h4>
                                <p class="text-xs text-brand-100 mt-0.5">Mulai transaksi baru</p>
                            </div>
                        </a>
                        @endrole

                        @role('Owner|Admin')
                        <a href="{{ route('warehouse.create') }}" class="flex items-center p-4 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 border border-gray-200 transition group">
                            <div class="bg-white p-3 rounded-lg border border-gray-200 mr-4 shadow-sm group-hover:scale-110 transition">
                                <span class="text-xl">📦</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Tambah Barang Baru</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Masukan stok ke gudang</p>
                            </div>
                        </a>
                        @endrole

                        @role('Viewer|Owner|Admin')
                        <a href="{{ route('analytics') }}" class="flex items-center p-4 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 border border-gray-200 transition group">
                            <div class="bg-white p-3 rounded-lg border border-gray-200 mr-4 shadow-sm group-hover:scale-110 transition">
                                <span class="text-xl">📊</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Lihat Analitik</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Cek laporan Laba/Rugi</p>
                            </div>
                        </a>
                        @endrole
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
