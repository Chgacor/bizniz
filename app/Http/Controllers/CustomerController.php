<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Customer::query();

        if ($request->has('search')) {
            $q = $request->search;
            $query->where('name', 'LIKE', "%{$q}%")
                ->orWhere('phone', 'LIKE', "%{$q}%");
        }

        // LOAD RELASI LENGKAP: Transactions -> Items -> Product
        // Ini kunci agar perhitungan 'Jasa' vs 'Part' bisa dilakukan
        $customers = $query->with(['transactions.items.product'])
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    // ... (Biarkan fungsi store, update, destroy tetap seperti kode Anda sebelumnya) ...
    // Pastikan fungsi di bawah ini ada untuk POS:

    public function search(Request $request)
    {
        // Validasi input agar tidak error jika kosong
        $query = $request->get('query');

        if (!$query) {
            return response()->json([]);
        }

        $customers = \App\Models\Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->take(10) // Limit hasil
            ->get(['id', 'name', 'phone', 'address']); // Hanya ambil kolom penting

        return response()->json($customers);
    }

    public function storeFromPos(Request $request)
    {
        $request->validate(['name' => 'required', 'phone' => 'required']);
        $c = Customer::create($request->all());
        return response()->json(['status' => 'success', 'customer' => $c]);
    }

    // CRUD Standar (Store, Update, Destroy)
    public function store(Request $request) {
        Customer::create($request->all()); return back();
    }
    public function update(Request $request, $id) {
        Customer::find($id)->update($request->all()); return back();
    }
    public function destroy($id) {
        Customer::find($id)->delete(); return back();
    }
}
