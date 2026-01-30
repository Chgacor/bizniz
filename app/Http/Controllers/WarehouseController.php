<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
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
            'category' => 'required|string', // Teks bebas
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Simpan
        $product = Product::create([
            'name' => $request->name,
            'product_code' => $request->product_code,
            'category' => $request->category,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'stock_quantity' => $request->stock_quantity,
            'image_path' => $imagePath,
        ]);

        // Catat Stok Awal
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => $request->stock_quantity,
            'description' => 'Stok Awal (Barang Baru)',
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // (Fungsi edit, update, destroy biarkan tetap ada seperti sebelumnya)
    public function edit($id)
    {
        // CEK IZIIN
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

        // Cek apakah stok berubah? Jika ya, catat log.
        $oldStock = $product->stock_quantity;
        $newStock = $request->stock_quantity;

        if ($newStock != $oldStock) {
            $diff = $newStock - $oldStock;
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out', // Masuk atau Keluar
                'quantity' => abs($diff),
                'description' => 'Koreksi Stok Manual (Edit Produk)',
            ]);
        }

        // Handle Upload Foto Baru
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($product->image_path && \Illuminate\Support\Facades\Storage::exists('public/' . $product->image_path)) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $product->image_path);
            }
            // Simpan foto baru
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
            // image_path sudah dihandle di atas
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    // 6. SHOW (Untuk jaga-jaga jika tombol View tertekan)
    public function show($id)
    {
        // Langsung lempar ke halaman edit saja biar praktis
        return redirect()->route('warehouse.edit', $id);
    }
    public function destroy($id)
    {
        // CEK IZIN
        if(auth()->user()->hasRole('Staff')) {
            abort(403, 'Akses Ditolak.');
        }

        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('warehouse.index')->with('success', 'Produk dihapus.');
    }
}
