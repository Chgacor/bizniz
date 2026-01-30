<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $transaction->invoice_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { width: 80mm; margin: 0; } /* Thermal Printer Standard */
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-50 text-black font-mono text-sm p-4 mx-auto max-w-[80mm] leading-tight">

<div class="text-center mb-4 border-b border-dashed border-black pb-2">
    <h1 class="text-xl font-bold uppercase">Bizniz.IO</h1>
    <p class="text-xs">Secure Internal Operations</p>
    <p class="text-xs mt-1">{{ now()->format('d M Y H:i') }}</p>
</div>

<div class="mb-2 text-xs">
    <p>INV: {{ $transaction->invoice_code }}</p>
    <p>CSH: {{ strtoupper($transaction->user->name) }}</p>
</div>

<table class="w-full mb-4 text-xs">
    <thead>
    <tr class="border-b border-black">
        <th class="text-left py-1">Item</th>
        <th class="text-right py-1">Qty</th>
        <th class="text-right py-1">Ttl</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaction->items as $item)
        <tr>
            <td class="py-1">{{ Str::limit($item->product->name, 15) }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->price_at_sale * $item->quantity, 0) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="border-t border-dashed border-black pt-2 mb-6">
    <div class="flex justify-between font-bold text-lg">
        <span>TOTAL</span>
        <span>{{ number_format($transaction->total_amount, 0) }}</span>
    </div>
    <div class="flex justify-between text-xs mt-1">
        <span>CASH</span>
        <span>{{ number_format($transaction->cash_received, 0) }}</span>
    </div>
    <div class="flex justify-between text-xs">
        <span>CHANGE</span>
        <span>{{ number_format($transaction->change_returned, 0) }}</span>
    </div>
</div>

<div class="text-center text-xs mt-4">
    <p>*** THANK YOU ***</p>
    <p>Internal Use Only</p>
</div>

<script>
    window.onload = function() { window.print(); }
</script>
</body>
</html>
