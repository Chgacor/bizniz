<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_code }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px 5px;
            width: 58mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; display: block; }
        .flex { display: flex; justify-content: space-between; }
        .items-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .items-table th { text-align: left; border-bottom: 1px dashed #000; padding-bottom: 3px; font-size: 11px; }
        .items-table td { padding: 3px 0; vertical-align: top; }
        .mb-1 { margin-bottom: 2px; }

        @media print {
            @page { margin: 0; size: auto; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="text-center">
    {{-- LOGO (Opsional) --}}
    @if(isset($settings['show_logo_on_receipt']) && $settings['show_logo_on_receipt'] == '1' && isset($settings['shop_logo']))
        <img src="{{ asset('storage/'.$settings['shop_logo']) }}" style="max-width: 40mm; margin-bottom: 5px;">
        <br>
    @endif

    {{-- NAMA TOKO --}}
    <h2 class="bold uppercase" style="margin: 0; font-size: 16px;">
        {{ $settings['business_name'] ?? config('app.name') }}
    </h2>

    {{-- ALAMAT --}}
    <div style="font-size: 11px; margin-top: 2px;">
        {{ $settings['address'] ?? 'Alamat Toko Belum Diatur' }}
    </div>

    {{-- TELEPON --}}
    <div style="font-size: 11px;">
        Telp: {{ $settings['phone'] ?? '-' }}
    </div>
</div>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex">
        <span>No: {{ $transaction->invoice_code }}</span>
        <span>{{ $transaction->created_at->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Kasir: {{ substr($transaction->user->name ?? 'Admin', 0, 15) }}</span>
    </div>
    <div class="flex">
        <span>Pel: {{ substr($transaction->customer->name ?? 'Umum', 0, 15) }}</span>
    </div>
</div>

<div class="line"></div>

<table class="items-table">
    <thead>
    <tr>
        <th style="width: 50%">Item</th>
        <th style="width: 15%" class="text-right">Qty</th>
        <th style="width: 35%" class="text-right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaction->items as $item)
        <tr>
            <td colspan="3" class="bold">
                {{-- PRIORITAS NAMA: 1. Nama di tabel item, 2. Nama di tabel produk, 3. Fallback --}}
                {{ $item->name ?? $item->product->name ?? 'Jasa/Barang Manual' }}
            </td>
        </tr>
        <tr>
            <td>
                <small>@ {{ number_format($item->price_at_sale, 0, ',', '.') }}</small>
            </td>
            <td class="text-right">x{{ $item->quantity }}</td>
            <td class="text-right">
                {{-- FIX: Hitung langsung biar gak 0 --}}
                {{ number_format($item->price_at_sale * $item->quantity, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex mb-1">
        <span>Subtotal</span>
        <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
    </div>

    @if($transaction->discount_amount > 0)
        <div class="flex mb-1">
            <span>Diskon</span>
            <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="line"></div>

    <div class="flex bold" style="font-size: 14px; margin: 5px 0;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
</div>

<div class="line"></div>

@php
    // FIX BUG BAYAR 0:
    // Jika paid_amount 0 tapi ada kembalian, kita hitung mundur (Total + Kembali)
    $uangBayar = $transaction->paid_amount;
    if($uangBayar <= 0 && $transaction->change_amount > 0) {
        $uangBayar = $transaction->total_amount + $transaction->change_amount;
    }
    // Jika masih 0 (pas bayar non-cash), samakan dengan total
    if($uangBayar <= 0) {
        $uangBayar = $transaction->total_amount;
    }
@endphp

<div style="font-size: 11px;">
    <div class="flex mb-1">
        <span class="uppercase">Bayar ({{ $transaction->payment_method ?? 'CASH' }})</span>
        <span>{{ number_format($uangBayar, 0, ',', '.') }}</span>
    </div>
    <div class="flex bold">
        <span>KEMBALI</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>
</div>

<div class="line"></div>

<div class="text-center" style="margin-top: 10px;">
    <p style="margin: 0; font-weight: bold;">
        {{ $settings['receipt_header'] ?? 'TERIMA KASIH' }}
    </p>
    <p style="margin: 3px 0 0 0; font-size: 10px;">
        {{ $settings['receipt_footer'] ?? 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.' }}
    </p>
</div>

</body>
</html>
