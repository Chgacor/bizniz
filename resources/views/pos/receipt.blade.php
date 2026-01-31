<!DOCTYPE html>
<html lang="id"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_code }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px; /* Sedikit dikecilkan agar muat item panjang */
            margin: 0;
            padding: 5px; /* Padding dikurangi biar hemat kertas */
            width: 56mm; /* Aman untuk printer 58mm */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 4px 0; }
        .flex { display: flex; justify-content: space-between; }

        /* Tabel dirapikan */
        .items-table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        .items-table th { text-align: left; border-bottom: 1px dashed #000; font-size: 10px; padding-bottom: 2px;}
        .items-table td { padding: 2px 0; vertical-align: top; }

        @media print {
            @page { margin: 0; size: auto; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="text-center">
    <h2 style="margin:0; font-size: 14px;">{{ $settings['business_name'] ?? 'BIZNIZ.IO' }}</h2>
    <p style="margin:2px 0; font-size: 10px;">{{ $settings['address'] ?? 'Alamat Toko Belum Diatur' }}</p>
    <p style="margin:2px 0; font-size: 10px;">{{ date('d/m/Y H:i', strtotime($transaction->created_at)) }}</p>
</div>

<div class="line"></div>

<div style="font-size: 10px;">
    INV: {{ $transaction->invoice_code }}<br>
    KSR: {{ $transaction->user->name ?? 'Admin' }}<br>
    PLG: {{ $transaction->customer->name ?? 'Umum' }}
</div>

<div class="line"></div>

<table class="items-table">
    <thead>
    <tr>
        <th style="width: 55%">Item</th>
        <th style="width: 10%" class="text-right">Qty</th>
        <th style="width: 35%" class="text-right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaction->items as $item)
        <tr>
            <td>
                {{ $item->product->name ?? $item->name }}
                <br>
                <small style="color:grey;">
                    @ {{ number_format($item->price ?? $item->price_at_sale, 0, ',', '.') }}
                </small>
            </td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">
                {{ number_format(($item->price ?? $item->price_at_sale) * $item->quantity, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="line"></div>

<div class="flex bold" style="font-size: 13px;">
    <span>TOTAL</span>
    <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
</div>

<div class="flex" style="margin-top: 4px;">
    <span>TUNAI</span>
    <span>{{ number_format($transaction->cash_received, 0, ',', '.') }}</span>
</div>

<div class="flex bold">
    <span>KEMBALI</span>
    <span>{{ number_format($transaction->change_amount ?? ($transaction->cash_received - $transaction->total_amount), 0, ',', '.') }}</span>
</div>

<div class="line"></div>

<div class="text-center" style="margin-top: 8px;">
    <p style="margin:0;">{{ $settings['receipt_footer'] ?? 'Terima Kasih!' }}</p>
    <p style="font-size: 9px; margin-top:2px; color:grey;">Powered by Bizniz.io</p>
</div>

</body>
</html>
