<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-brand-900 leading-tight">
            {{ __('Proses Retur Barang') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
                <form method="GET" action="{{ route('returns.create') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Scan / Input Nomor Invoice</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" name="invoice_code" value="{{ request('invoice_code') }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 focus:ring-brand-500 focus:border-brand-500 font-mono text-lg font-bold uppercase placeholder-gray-300"
                                   placeholder="INV-XXXXXXXX" autofocus>
                        </div>
                    </div>
                    <button type="submit" class="bg-brand-900 text-white font-bold py-3 px-8 rounded-xl hover:bg-black shadow-lg transition transform hover:-translate-y-0.5">
                        Cari Transaksi
                    </button>
                </form>
            </div>

            @if(request('invoice_code'))
                @if(isset($transaction) && $transaction)
                    <form action="{{ route('returns.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

                            <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg">Invoice #{{ $transaction->invoice_code }}</h3>
                                    <p class="text-xs text-orange-600 font-mono mt-1">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-gray-500 uppercase">Pelanggan</span>
                                    <div class="font-bold text-gray-900">{{ $transaction->customer->name ?? 'Umum' }}</div>
                                </div>
                            </div>

                            <div class="p-6">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                                        <th class="py-3">Produk</th>
                                        <th class="py-3 text-center">Qty Beli</th>
                                        <th class="py-3 text-center" width="120">Qty Retur</th>
                                        <th class="py-3 text-left pl-4" width="200">Kondisi</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                    @foreach($transaction->items as $item)
                                        <tr class="group hover:bg-gray-50 transition">
                                            <td class="py-4 align-top">
                                                {{-- FIX: Ambil nama dari snapshot transaksi dulu, baru master produk --}}
                                                <div class="font-bold text-gray-900 text-lg">
                                                    {{ $item->name ?? $item->product->name ?? 'Item Manual / Terhapus' }}
                                                </div>
                                                <div class="text-xs text-gray-400 font-mono mt-1">
                                                    @ {{ number_format($item->price_at_sale, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="py-4 text-center font-bold align-top pt-5 text-gray-500">
                                                {{ $item->quantity }}
                                            </td>

                                            {{-- INPUT QTY RETUR --}}
                                            <td class="py-4 text-center align-top">
                                                <input type="number" name="items[{{ $item->id }}][qty_return]"
                                                       min="0" max="{{ $item->quantity }}" value="0"
                                                       class="w-20 rounded-lg border-gray-300 text-center font-bold text-lg focus:ring-red-500 focus:border-red-500">
                                            </td>

                                            {{-- INPUT KONDISI --}}
                                            <td class="py-4 pl-4 align-top">
                                                <select name="items[{{ $item->id }}][condition]" class="w-full text-sm rounded-lg border-gray-300 focus:ring-red-500 font-bold">
                                                    <option value="good">✅ Layak Jual</option>
                                                    <option value="bad">❌ Rusak / Cacat</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-8 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Retur</label>
                                    <textarea name="reason" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-red-500 placeholder-gray-400" placeholder="Contoh: Salah ukuran, barang cacat..."></textarea>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                                <a href="{{ route('returns.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50">Batal</a>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                    <span>🔄</span> Proses Retur
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="bg-white border-l-4 border-red-500 rounded-xl p-8 text-center shadow-sm">
                        <svg class="w-16 h-16 text-red-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-xl font-bold text-gray-800">Transaksi Tidak Ditemukan</h3>
                        <p class="text-gray-500 mt-2">Nomor invoice <b>{{ request('invoice_code') }}</b> tidak ada di database.</p>
                        <a href="{{ route('returns.create') }}" class="inline-block mt-4 text-red-600 font-bold hover:underline">Coba lagi</a>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
