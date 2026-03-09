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
            width: 58mm; /* Lebar standar thermal printer EPPOS */
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; display: block; }
        .flex { display: flex; justify-content: space-between; }
        .items-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
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
    {{-- LOGO TOKO --}}
    @if(!empty($settings['show_logo_on_receipt']) && $settings['show_logo_on_receipt'] == '1' && !empty($settings['shop_logo']))
        <img src="{{ asset('storage/' . $settings['shop_logo']) }}" style="max-width: 45mm; max-height: 30mm; object-fit: contain; margin-bottom: 5px;">
        <br>
    @endif

    {{-- NAMA TOKO --}}
    <h2 class="bold" style="margin: 0; font-size: 16px;">
        {{ !empty($settings['business_name']) ? $settings['business_name'] : config('app.name') }}
    </h2>

    {{-- ALAMAT --}}
    <div style="font-size: 11px; margin-top: 2px;">
        {{ !empty($settings['address']) ? $settings['address'] : 'Alamat Toko Belum Diatur' }}
    </div>

    {{-- TELEPON --}}
    <div style="font-size: 11px;">
        Telp: {{ !empty($settings['phone']) ? $settings['phone'] : '-' }}
    </div>
</div>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex">
        <span>Nota: {{ $transaction->invoice_code }}</span>
    </div>
    <div class="flex">
        <span>Tgl : {{ $transaction->created_at->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Plg : {{ $transaction->customer->name ?? 'Umum' }}</span>
    </div>
</div>

<div class="line"></div>

<table class="items-table">
    <tbody>
    @foreach($transaction->items as $item)
        <tr>
            <td colspan="2" class="bold">
                {{ $item->name ?? $item->product->name ?? 'Item' }}
            </td>
        </tr>
        <tr>
            <td>
                {{ $item->quantity }} x {{ number_format($item->price_at_sale, 0, ',', '.') }}
            </td>
            <td class="text-right">
                {{ number_format($item->subtotal, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex mb-1">
        <span>Subtotal</span>
        <span>{{ !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : 'Rp' }} {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
    </div>

    @if($transaction->discount_amount > 0)
        <div class="flex mb-1">
            <span>Diskon</span>
            <span>-{{ !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : 'Rp' }} {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
    @endif

    {{-- PAJAK PPN --}}
    @if($transaction->tax_amount > 0)
        <div class="flex mb-1">
            <span>PPN ({{ !empty($settings['tax_rate']) ? $settings['tax_rate'] : 0 }}%)</span>
            <span>{{ !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : 'Rp' }} {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="line"></div>

    <div class="flex bold" style="font-size: 14px; margin: 5px 0;">
        <span>TOTAL</span>
        <span>{{ !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : 'Rp' }} {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
</div>

@php
    // LOGIKA PERBAIKAN BAYAR (Agar tidak 0)
    $uangBayar = $transaction->paid_amount;
    if($uangBayar <= 0) {
        $uangBayar = $transaction->total_amount + $transaction->change_amount;
    }
@endphp

<div style="font-size: 11px;">
    <div class="flex mb-1">
        <span>Bayar ({{ strtoupper($transaction->payment_method ?? 'CASH') }})</span>
        <span>{{ number_format($uangBayar, 0, ',', '.') }}</span>
    </div>
    <div class="flex bold">
        <span>Kembali</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>
</div>

<div class="line"></div>

<div class="text-center" style="margin-top: 10px;">
    <p style="margin: 0; font-weight: bold;">
        {{ !empty($settings['receipt_header']) ? $settings['receipt_header'] : 'TERIMA KASIH' }}
    </p>
    <p style="margin: 3px 0 0 0; font-size: 10px;">
        {{ !empty($settings['receipt_footer']) ? $settings['receipt_footer'] : '' }}
    </p>
</div>

</body>
</html>
