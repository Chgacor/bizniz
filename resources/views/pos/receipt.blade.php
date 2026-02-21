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
            padding: 5px;
            width: 48mm; /* Lebar efektif untuk 58mm printer */
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; display: block; }
        .flex { display: flex; justify-content: space-between; }
        .items-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .items-table td { padding: 2px 0; vertical-align: top; }

        /* Pengaturan agar RawBT tidak menampilkan header browser */
        @media print {
            @page { margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
{{-- Fungsi window.print() akan memicu RawBT jika dibuka lewat URL rawbt: --}}
<body onload="window.print()">

<div class="text-center">
    {{-- LOGO BIZNIZ.IO --}}
    <div style="font-size: 18px; font-weight: bold; background: #000; color: #fff; display: inline-block; padding: 2px 8px; border-radius: 4px;">B</div>
    <h2 class="bold" style="margin: 5px 0 0 0; font-size: 16px;">BIZNIZ.IO</h2>
    <div style="font-size: 10px;">{{ $settings['address'] ?? 'Bengkel Profesional Anda' }}</div>
    <div style="font-size: 10px;">Telp: {{ $settings['phone'] ?? '-' }}</div>
</div>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex"><span>Nota: {{ $transaction->invoice_code }}</span></div>
    <div class="flex"><span>Tgl : {{ $transaction->created_at->format('d/m/y H:i') }}</span></div>
    <div class="flex"><span>Plg : {{ \Illuminate\Support\Str::limit($transaction->customer->name ?? 'Umum', 15) }}</span></div>
</div>

<div class="line"></div>

<table class="items-table">
    @foreach($transaction->items as $item)
        <tr>
            <td colspan="2" class="bold">{{ $item->name ?? $item->product->name }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">
                {{ $item->quantity }} x {{ number_format($item->price_at_sale, 0, ',', '.') }}
            </td>
            <td class="text-right" style="font-size: 11px;">
                {{ number_format($item->price_at_sale * $item->quantity, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
</table>

<div class="line"></div>

<div style="font-size: 11px;">
    <div class="flex">
        <span>TOTAL</span>
        <span class="bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex">
        <span>Bayar</span>
        <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex bold">
        <span>Kembali</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>
</div>

<div class="line"></div>

<div class="text-center" style="margin-top: 5px; font-size: 10px;">
    <p class="bold">TERIMA KASIH</p>
    <p>Simpan struk ini sebagai bukti garansi.</p>
</div>

{{-- Spasi agar kertas keluar cukup panjang untuk disobek --}}
<br><br><br>

</body>
</html>
