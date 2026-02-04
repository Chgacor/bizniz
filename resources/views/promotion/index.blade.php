<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('🎟️ Kelola Promo & Diskon') }}
            </h2>
            <a href="{{ route('promotions.create') }}" class="bg-black hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg text-sm shadow flex items-center gap-2">
                <span>+ Buat Promo Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200 shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 uppercase font-bold text-gray-500 text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Nama Promo</th>
                            <th class="px-6 py-4 text-center">Besar Diskon</th>
                            <th class="px-6 py-4 text-center">Periode Berlaku</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($promotions as $promo)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $promo->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($promo->description, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($promo->discount_type == 'fixed')
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold text-xs border border-green-200">
                                                Rp {{ number_format($promo->value, 0, ',', '.') }}
                                            </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200">
                                                Diskon {{ $promo->value }}%
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-gray-600">
                                    @if(!$promo->start_date && !$promo->end_date)
                                        <span class="text-brand-600 font-bold">Selamanya ♾️</span>
                                    @else
                                        <div class="flex flex-col gap-1">
                                            <span>Mulai: {{ $promo->start_date ? $promo->start_date->format('d M Y') : 'Sekarang' }}</span>
                                            <span>Sampai: {{ $promo->end_date ? $promo->end_date->format('d M Y') : 'Seterusnya' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('promotions.toggle', $promo->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-[10px] font-bold uppercase px-2 py-1 rounded border transition {{ $promo->is_active ? 'bg-green-50 text-green-600 border-green-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200' : 'bg-gray-100 text-gray-400 border-gray-200 hover:bg-green-50 hover:text-green-600' }}"
                                                title="Klik untuk ubah status">
                                            {{ $promo->is_active ? '✅ AKTIF' : '⛔ NON-AKTIF' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('promotions.edit', $promo->id) }}" class="text-gray-400 hover:text-blue-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('promotions.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini selamanya?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 bg-gray-50">
                                    <div class="flex flex-col items-center">
                                        <span class="text-4xl mb-2">🏷️</span>
                                        <p class="text-sm">Belum ada promo yang dibuat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $promotions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
