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
            /* Standard Thermal Paper Width */
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
            display: block;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        /* Table Styling */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
            font-size: 11px;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .mb-1 {
            margin-bottom: 2px;
        }

        /* Invoice Code Styling */
        .invoice-code {
            font-size: 11px;
            letter-spacing: 0.5px;
            word-break: break-all;
        }

        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="text-center">
        @if (isset($settings['show_logo_on_receipt']) &&
                $settings['show_logo_on_receipt'] == '1' &&
                isset($settings['shop_logo']))
            <img src="{{ asset('storage/' . $settings['shop_logo']) }}" style="max-width: 40mm; margin-bottom: 5px;">
            <br>
        @endif

        <h2 class="bold uppercase" style="margin: 0; font-size: 16px;">
            {{ $settings['business_name'] ?? config('app.name') }}
        </h2>

        <div style="font-size: 11px; margin-top: 2px;">
            {{ $settings['address'] ?? 'Alamat Toko Belum Diatur' }}
        </div>

        <div style="font-size: 11px;">
            Telp: {{ $settings['phone'] ?? '-' }}
        </div>
    </div>

    <div class="line"></div>

    <div class="text-center" style="margin-bottom: 5px;">
        <span style="font-size: 10px;">NO. TRANSAKSI</span><br>
        <span class="bold invoice-code">{{ $transaction->invoice_code }}</span>
    </div>

    <div style="font-size: 11px;">
        <div class="flex">
            <span>{{ $transaction->created_at->format('d/m/y H:i') }}</span>
            <span>Kasir: {{ $transaction->user->name ?? 'Admin' }}</span>
        </div>
        <div class="text-left" style="margin-top: 2px;">
            Pel: {{ $transaction->customer->name ?? 'Umum' }}
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
            @foreach ($transaction->items as $item)
                <tr>
                    <td colspan="3" class="bold">
                        {{ $item->name ?? ($item->product->name ?? 'Item') }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <small>@ {{ number_format($item->price_at_sale, 0, ',', '.') }}</small>
                    </td>
                    <td class="text-right">x{{ $item->quantity }}</td>
                    <td class="text-right">
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

        @if ($transaction->discount_amount > 0)
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
        $uangBayar = $transaction->paid_amount;
        if ($uangBayar <= 0 && $transaction->change_amount > 0) {
            $uangBayar = $transaction->total_amount + $transaction->change_amount;
        }
        if ($uangBayar <= 0) {
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
            {{ $settings['receipt_footer'] ?? 'Simpan struk ini sebagai bukti pembayaran yang sah.' }}
        </p>
    </div>

</body>

</html>
