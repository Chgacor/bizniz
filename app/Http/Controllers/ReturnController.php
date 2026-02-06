<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn; // Model Header (Tabel returns)
use App\Models\ReturnItem;  // Model Detail (Tabel return_items)
use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockMovement;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        // Load data
        $returns = SalesReturn::with(['transaction', 'user', 'items.product'])
            ->latest()
            ->paginate(10);

        // PERBAIKAN: Folder view pakai 'return' (singular)
        return view('return.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $transaction = null;

        if ($request->has('invoice_code')) {
            $code = $request->invoice_code;

            $transaction = Transaction::with(['items.product' => function($q) {
                $q->withTrashed();
            }, 'customer'])
                ->where('invoice_code', $code)
                ->first();
        }

        // PERBAIKAN: Folder view pakai 'return' (singular)
        return view('return.create', compact('transaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items' => 'required|array',
            'reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $hasReturn = false;
            $itemsToReturn = [];
            $totalRefund = 0; // Variabel untuk menampung total uang kembali

            // 1. Validasi & Hitung Total Refund DULU
            foreach ($request->items as $itemId => $data) {
                if (isset($data['qty_return']) && $data['qty_return'] > 0) {
                    $hasReturn = true;
                    $trxItem = TransactionItem::find($itemId);

                    // Validasi Qty
                    if ($data['qty_return'] > $trxItem->quantity) {
                        throw new \Exception("Qty retur melebihi qty beli untuk item: " . $trxItem->name);
                    }

                    // Hitung nominal refund per item
                    $refundAmount = $trxItem->price_at_sale * $data['qty_return'];

                    // Tambahkan ke total keseluruhan (INI YANG BIKIN ERROR SEBELUMNYA KARENA KOSONG)
                    $totalRefund += $refundAmount;

                    $itemsToReturn[] = [
                        'trx_item' => $trxItem,
                        'qty' => $data['qty_return'],
                        'condition' => $data['condition'],
                        'refund' => $refundAmount
                    ];
                }
            }

            if (!$hasReturn) {
                return back()->with('error', 'Pilih minimal 1 barang untuk diretur.');
            }

            // Generate Kode Retur: RET-YYYYMMDD-XXX
            $today = date('Ymd');
            $count = SalesReturn::whereDate('created_at', today())->count() + 1;
            $returnCode = 'RET-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header (SalesReturn)
            $salesReturn = SalesReturn::create([
                'return_code' => $returnCode,
                'transaction_id' => $request->transaction_id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'total_refund' => $totalRefund, // <--- SUDAH DITAMBAHKAN (SOLUSI ERROR 1364)
            ]);

            // 3. Simpan Detail Item (ReturnItem) & Update Stok
            foreach ($itemsToReturn as $data) {
                $trxItem = $data['trx_item'];

                ReturnItem::create([
                    'return_id' => $salesReturn->id,
                    'product_id' => $trxItem->product_id,
                    'quantity' => $data['qty'],
                    'condition' => $data['condition'],
                    'refund_amount' => $data['refund'],
                ]);

                // 4. Kembalikan Stok (Hanya jika kondisi BAGUS & Produk ada)
                if ($data['condition'] === 'good' && $trxItem->product_id) {
                    $product = Product::find($trxItem->product_id);
                    if ($product && $product->type === 'goods') {
                        $product->increment('stock_quantity', $data['qty']);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'type' => 'in',
                            'quantity' => $data['qty'],
                            'description' => 'Retur ' . $returnCode,
                        ]);
                    }
                }
            }

            DB::commit();

            // PERBAIKAN: Redirect ke route index (pastikan nama route di web.php sesuai)
            // Jika nama route bapak 'returns.index', biarkan. Jika 'return.index', ubah di sini.
            return redirect()->route('returns.index')->with('success', 'Retur berhasil! Kode: ' . $returnCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
