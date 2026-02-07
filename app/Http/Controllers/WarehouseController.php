<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Ambil Data Barang (Goods)
        $goods = Product::where('type', 'goods')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                }
            })
            ->latest()->paginate(10, ['*'], 'goods_page')->withQueryString();

        // Ambil Data Jasa (Service)
        $services = Product::where('type', 'service')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                }
            })
            ->latest()->paginate(10, ['*'], 'services_page')->withQueryString();

        return view('warehouse.index', compact('goods', 'services'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        // Hitung urutan selanjutnya KHUSUS BARANG untuk preview
        $countGoods = Product::where('type', 'goods')->count();
        $nextSequence = str_pad($countGoods + 1, 8, '0', STR_PAD_LEFT);

        return view('warehouse.create', compact('categories', 'nextSequence'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'type' => 'required|in:goods,service',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'product_code' => 'nullable|unique:products,product_code',
        ]);

        DB::beginTransaction();
        try {
            $category = Category::firstOrCreate(['name' => $request->category]);

            $productCode = null;

            if ($request->type === 'goods') {
                $productCode = $this->generateProductCode($request->name);
            } else {
                $productCode = 'SRV-' . time() . rand(10,99);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $type = $request->type;
            $stock = ($type === 'service') ? 0 : ($request->stock_quantity ?? 0);
            $buyPrice = ($type === 'service') ? 0 : ($request->buy_price ?? 0);

            $product = Product::create([
                'product_code' => $productCode,
                'name' => $request->name,
                'category' => $category->name,
                'type' => $type,
                'buy_price' => $buyPrice,
                'sell_price' => $request->sell_price,
                'stock_quantity' => $stock,
                'image_path' => $imagePath,
            ]);

            if ($type === 'goods' && $stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $stock,
                    'description' => 'Stok Awal (Manual)',
                ]);
            }

            DB::commit();

            $pesan = ($type === 'goods')
                ? 'Barang berhasil ditambah! Kode: ' . $productCode
                : 'Jasa service berhasil ditambahkan!';

            return redirect()->route('warehouse.index')->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('warehouse.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'type' => 'required|in:goods,service',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $category = Category::firstOrCreate(['name' => $request->category]);

            if ($request->hasFile('image')) {
                if ($product->image_path && Storage::exists('public/' . $product->image_path)) {
                    Storage::delete('public/' . $product->image_path);
                }
                $product->image_path = $request->file('image')->store('products', 'public');
            }

            $type = $request->type;
            $newStock = ($type === 'service') ? 0 : ($request->stock_quantity ?? 0);

            if ($type === 'goods') {
                $oldStock = $product->stock_quantity;
                if ($newStock != $oldStock) {
                    $diff = $newStock - $oldStock;
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                        'quantity' => abs($diff),
                        'description' => 'Koreksi Stok (Edit)',
                    ]);
                }
            }

            if ($type === 'goods' && (str_contains($product->product_code, 'SRV-') || $product->product_code === null)) {
                $product->product_code = $this->generateProductCode($request->name);
            }
            if ($type === 'service') {
                $product->product_code = 'SRV-' . time() . rand(10,99);
            }

            $product->update([
                'name' => $request->name,
                'category' => $category->name,
                'type' => $type,
                'buy_price' => ($type === 'service') ? 0 : $request->buy_price,
                'sell_price' => $request->sell_price,
                'stock_quantity' => $newStock,
                'product_code' => $product->product_code,
            ]);

            DB::commit();
            return redirect()->route('warehouse.index')->with('success', 'Data diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('warehouse.index')->with('success', 'Item dihapus.');
    }

    private function generateProductCode($name)
    {
        $words = preg_split("/\s+/", $name);
        $initials = "";

        foreach ($words as $word) {
            $cleanChar = preg_replace('/[^a-zA-Z0-9]/', '', substr($word, 0, 1));
            $initials .= strtolower($cleanChar);
        }

        if (empty($initials)) $initials = "x";

        $countGoods = Product::where('type', 'goods')->count();
        $nextSequence = $countGoods + 1;

        return $initials . str_pad($nextSequence, 8, '0', STR_PAD_LEFT);
    }
}
