<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // =========================================================
    // UPDATE UTAMA ADA DI FUNGSI INDEX INI
    // =========================================================
    public function index(Request $request)
    {
        // 1. Mulai Query Builder
        $query = Product::query();

        // 2. Cek apakah ada pencarian?
        if ($request->filled('search')) {
            $search = $request->search;

            // Cari berdasarkan Nama ATAU Kode Produk
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // 3. Ambil data (terbaru) & Paginate
        // withQueryString() penting agar saat pindah halaman (page 2), pencarian tidak hilang
        $products = $query->latest()->paginate(10)->withQueryString();

        return view('warehouse.index', compact('products'));
    }

    public function create()
    {
        return view('warehouse.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|unique:products,product_code',
            'category' => 'required|string',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            // Tambahkan validasi tipe jika belum ada di form, default bisa 'goods'
            'type' => 'nullable|in:goods,service',
        ]);

        // Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Tentukan tipe (jika tidak dikirim form, anggap goods)
        $type = $request->type ?? 'goods';

        // Simpan
        $product = Product::create([
            'name' => $request->name,
            'product_code' => $request->product_code,
            'category' => $request->category,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'stock_quantity' => $request->stock_quantity,
            'image_path' => $imagePath,
            'type' => $type, // Pastikan kolom ini ada di database/model
        ]);

        // Catat Stok Awal (Hanya jika tipe barang fisik)
        if($type === 'goods') {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $request->stock_quantity,
                'description' => 'Stok Awal (Barang Baru)',
            ]);
        }

        return redirect()->route('warehouse.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit($id)
    {
        if(auth()->user()->hasRole('Staff')) {
            abort(403, 'Akses Ditolak: Staff tidak diperbolehkan mengedit barang.');
        }

        $product = Product::findOrFail($id);
        return view('warehouse.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        if(auth()->user()->hasRole('Staff')) {
            abort(403, 'Akses Ditolak.');
        }

        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Cek Logika Stok (Hanya untuk Barang Fisik)
        if ($product->type === 'goods' || $product->type === null) {
            $oldStock = $product->stock_quantity;
            $newStock = $request->stock_quantity;

            if ($newStock != $oldStock) {
                $diff = $newStock - $oldStock;
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($diff),
                    'description' => 'Koreksi Stok Manual (Edit Produk)',
                ]);
            }
        }

        // Handle Upload Foto Baru
        if ($request->hasFile('image')) {
            if ($product->image_path && \Illuminate\Support\Facades\Storage::exists('public/' . $product->image_path)) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image_path = $imagePath;
        }

        // Update Data Produk
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'stock_quantity' => $request->stock_quantity,
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function show($id)
    {
        return redirect()->route('warehouse.edit', $id);
    }

    public function destroy($id)
    {
        if(auth()->user()->hasRole('Staff')) {
            abort(403, 'Akses Ditolak.');
        }

        $product = Product::findOrFail($id);

        // Hapus gambar fisik jika ada
        if ($product->image_path && \Illuminate\Support\Facades\Storage::exists('public/' . $product->image_path)) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $product->image_path);
        }

        $product->delete();
        return redirect()->route('warehouse.index')->with('success', 'Produk dihapus.');
    }
}
