<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight flex items-center">
            {{ __('Data Pelanggan (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen" x-data="{ showModal: false, editMode: false, form: { id: null, name: '', phone: '', email: '', address: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <form method="GET" action="{{ route('customers.index') }}" class="w-full md:w-1/3 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No HP..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 focus:ring-brand-500 focus:border-brand-500 shadow-sm transition">
                </form>

                <button @click="showModal = true; editMode = false; form = {id: null, name:'', phone:'', email:'', address:''}"
                        class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center">
                    <span class="mr-2 text-xl">+</span> Tambah Pelanggan
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Profil</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Riwayat Belanja</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $c)

                        {{-- =========================================== --}}
                        {{-- LOGIKA HITUNG (FIXED & OPTIMIZED) --}}
                        {{-- =========================================== --}}
                        @php
                            $totalPart = 0;
                            $totalJasa = 0;
                            $visitCount = 0;

                            if($c->transactions) {
                                $visitCount = $c->transactions->count();

                                foreach($c->transactions as $trx) {
                                    if($trx->items) {
                                        foreach($trx->items as $item) {
                                            // FIX: Prioritaskan 'price_at_sale' (snapshot)
                                            $hargaFix = $item->price_at_sale ?? $item->price ?? 0;
                                            $subtotal = $item->quantity * $hargaFix;

                                            // Logika Deteksi Jasa vs Part
                                            $isService = ($item->product_id === null) ||
                                                         ($item->product && $item->product->type === 'service');

                                            if($isService) {
                                                $totalJasa += $subtotal;
                                            } else {
                                                $totalPart += $subtotal;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        {{-- =========================================== --}}

                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-lg mr-4 border border-brand-200 shadow-sm">
                                        {{ substr($c->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-brand-600 transition">{{ $c->name }}</div>
                                        <div class="text-xs text-gray-400">Join: {{ $c->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700 font-medium">{{ $c->phone }}</div>
                                <div class="text-xs text-gray-500 truncate w-32" title="{{ $c->address }}">{{ $c->address }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-3">

                                    <div class="bg-services-50 px-3 py-2 rounded-lg border border-services-100 min-w-[110px] shadow-sm">
                                        <div class="text-[10px] text-services-700 font-bold uppercase tracking-wider mb-0.5 flex items-center">
                                            <span class="mr-1">🛠️</span> Jasa
                                        </div>
                                        <div class="text-sm font-bold text-services-700">Rp {{ number_format($totalJasa, 0, ',', '.') }}</div>
                                    </div>

                                    <div class="bg-goods-50 px-3 py-2 rounded-lg border border-goods-100 min-w-[110px] shadow-sm">
                                        <div class="text-[10px] text-goods-700 font-bold uppercase tracking-wider mb-0.5 flex items-center">
                                            <span class="mr-1">📦</span> Barang
                                        </div>
                                        <div class="text-sm font-bold text-goods-700">Rp {{ number_format($totalPart, 0, ',', '.') }}</div>
                                    </div>

                                    <div class="flex flex-col justify-center text-right pl-4 border-l border-gray-200">
                                        <span class="text-xs text-gray-400 font-medium">Transaksi</span>
                                        <span class="font-bold text-gray-700 text-lg">{{ $visitCount }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="showModal = true; editMode = true; form = { id: {{ $c->id }}, name: '{{ $c->name }}', phone: '{{ $c->phone }}', email: '{{ $c->email }}', address: '{{ $c->address }}' }"
                                        class="bg-white border border-gray-200 text-gray-500 hover:text-brand-600 hover:border-brand-300 px-3 py-1.5 rounded-lg transition shadow-sm mx-1">
                                    Edit
                                </button>

                                <form action="{{ route('customers.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini? Data transaksi tidak akan hilang.');">
                                    @csrf @method('DELETE')
                                    <button class="bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-300 px-3 py-1.5 rounded-lg transition shadow-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-base font-medium">Belum ada data pelanggan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    {{ $customers->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <form :action="editMode ? '/customers/' + form.id : '{{ route('customers.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl font-bold text-brand-900 mb-6 border-b pb-2" x-text="editMode ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'"></h3>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="form.name" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition" required placeholder="Contoh: Budi Santoso">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp / Telepon <span class="text-red-500">*</span></label>
                                    <input type="number" name="phone" x-model="form.phone" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition" required placeholder="0812...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email (Opsional)</label>
                                    <input type="email" name="email" x-model="form.email" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition" placeholder="budi@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                    <textarea name="address" x-model="form.address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition" placeholder="Jl. Raya No. 1..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-brand-600 text-base font-medium text-white hover:bg-brand-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                Simpan Data
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
