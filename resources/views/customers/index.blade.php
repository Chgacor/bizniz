<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight flex items-center">
            {{ __('Data Pelanggan (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen" x-data="{ showModal: false, editMode: false, form: { id: null, name: '', phone: '', email: '', address: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <form method="GET" action="{{ route('customers.index') }}" class="w-full md:w-1/3 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No HP..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 focus:ring-brand-500 shadow-sm">
                </form>
                <button @click="showModal = true; editMode = false; form = {id: null, name:'', phone:'', email:'', address:''}"
                        class="bg-brand-900 hover:bg-black text-white font-bold py-2.5 px-6 rounded-xl shadow-lg">
                    + Tambah Pelanggan
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Profil</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Riwayat Belanja</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $c)

                        {{-- =========================================== --}}
                        {{-- LOGIKA HITUNG (DIPERBAIKI) --}}
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
                                            // FIX: Prioritaskan 'price_at_sale' (snapshot harga saat beli)
                                            // Jika kosong, baru ambil 'price' master. Jika kosong juga, 0.
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
                                    <div class="h-12 w-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-lg mr-4 border border-brand-100">
                                        {{ substr($c->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $c->name }}</div>
                                        <div class="text-xs text-gray-400">Join: {{ $c->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $c->phone }}</div>
                                <div class="text-xs text-gray-500 truncate w-32">{{ $c->address }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-4">

                                    <div class="bg-blue-50 px-3 py-2 rounded-lg border border-blue-100 min-w-[100px]">
                                        <div class="text-[10px] text-blue-500 font-bold uppercase tracking-wider mb-0.5">Jasa / Service</div>
                                        <div class="text-sm font-bold text-blue-700">Rp {{ number_format($totalJasa, 0, ',', '.') }}</div>
                                    </div>

                                    <div class="bg-orange-50 px-3 py-2 rounded-lg border border-orange-100 min-w-[100px]">
                                        <div class="text-[10px] text-orange-500 font-bold uppercase tracking-wider mb-0.5">Sparepart</div>
                                        <div class="text-sm font-bold text-brand-700">Rp {{ number_format($totalPart, 0, ',', '.') }}</div>
                                    </div>

                                    <div class="flex flex-col justify-center text-right pl-2 border-l border-gray-100">
                                        <span class="text-xs text-gray-400">Kunjungan</span>
                                        <span class="font-bold text-gray-700">{{ $visitCount }}x</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="showModal = true; editMode = true; form = { id: {{ $c->id }}, name: '{{ $c->name }}', phone: '{{ $c->phone }}', email: '{{ $c->email }}', address: '{{ $c->address }}' }"
                                        class="text-gray-400 hover:text-brand-600 transition mx-2 font-bold">Edit</button>

                                <form action="{{ route('customers.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus?');">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600 transition font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data pelanggan.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $customers->withQueryString()->links() }}</div>
            </div>
        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full relative z-10">
                    <form :action="editMode ? '/customers/' + form.id : '{{ route('customers.store') }}'" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">
                        <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit Pelanggan' : 'Tambah Pelanggan'"></h3>

                        <div class="space-y-4">
                            <input type="text" name="name" x-model="form.name" placeholder="Nama Lengkap" class="w-full rounded border-gray-300" required>
                            <input type="number" name="phone" x-model="form.phone" placeholder="No HP" class="w-full rounded border-gray-300" required>
                            <input type="email" name="email" x-model="form.email" placeholder="Email" class="w-full rounded border-gray-300">
                            <textarea name="address" x-model="form.address" placeholder="Alamat" class="w-full rounded border-gray-300"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end space-x-2">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-brand-900 text-white rounded font-bold">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
