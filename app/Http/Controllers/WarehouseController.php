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

        $goods = Product::where('type', 'goods')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                }
            })
            ->latest()->paginate(10, ['*'], 'goods_page')->withQueryString();

        $services = Product::where('type', 'service')
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                }
            })
            ->latest()->paginate(10, ['*'], 'services_page')->withQueryString();

        return view('warehouse.index', compact('goods', 'services'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('warehouse.create', compact('categories'));
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
        ]);

        DB::beginTransaction();
        try {
            $category = Category::firstOrCreate(['name' => $request->category]);

            $productCode = Product::generateId($request->name);

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
            return redirect()->route('warehouse.index')->with('success', 'Produk berhasil ditambahkan: ' . $productCode);

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

            $product->update([
                'name' => $request->name,
                'category' => $category->name,
                'type' => $type,
                'buy_price' => ($type === 'service') ? 0 : $request->buy_price,
                'sell_price' => $request->sell_price,
                'stock_quantity' => $newStock,
            ]);

            DB::commit();
            return redirect()->route('warehouse.index')->with('success', 'Data produk diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('warehouse.index')->with('success', 'Produk dihapus (Soft Delete).');
    }
}
