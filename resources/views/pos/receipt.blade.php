<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_code }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk kasir */
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 58mm; /* Lebar standar kertas thermal */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
        .items-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .items-table th { text-align: left; border-bottom: 1px dashed #000; }
        .items-table td { padding-top: 2px; }

        @media print {
            @page { margin: 0; }
            body { margin: 0; padding: 5px; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="text-center">
    <h2 style="margin:0;">{{ $settings['business_name'] ?? 'BIZNIZ.IO' }}</h2>
    <p style="margin:2px 0;">{{ $settings['receipt_header'] ?? 'Secure Internal Operations' }}</p>
    <p style="margin:2px 0;">{{ $settings['address'] ?? '' }}</p>
    <p style="margin:2px 0;">{{ date('d M Y H:i', strtotime($transaction->created_at)) }}</p>
</div>

<div class="line"></div>

<div>
    INV: {{ $transaction->invoice_code }}<br>
    KSR: {{ $transaction->user->name ?? 'Admin' }}<br>
    PLG: {{ $transaction->customer->name ?? 'Umum' }}
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
            <td>
                {{ $item->product->name ?? $item->name }} <br>
                <small style="color:grey">@ {{ number_format($item->price) }}</small>
            </td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->price * $item->quantity) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="line"></div>

<div class="flex bold" style="font-size: 14px;">
    <span>TOTAL</span>
    <span>{{ number_format($transaction->total_amount) }}</span>
</div>

<div class="flex" style="margin-top: 5px;">
    <span>TUNAI (CASH)</span>
    <span>{{ number_format($transaction->cash_received) }}</span>
</div>

<div class="flex bold">
    <span>KEMBALI (CHANGE)</span>
    <span>{{ number_format($transaction->cash_received - $transaction->total_amount) }}</span>
</div>

<div class="line"></div>

<div class="text-center" style="margin-top: 10px;">
    <p>{{ $settings['receipt_footer'] ?? 'Terima Kasih telah berbelanja!' }}</p>
    <p style="font-size: 10px;">Powered by Bizniz.io</p>
</div>

</body>
</html>
