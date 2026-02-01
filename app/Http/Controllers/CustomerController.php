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
        $query = trim($request->get('query')); // Trim whitespace

        // Return empty array if search is too short (optional, but good for performance)
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $customers = \App\Models\Customer::query()
            ->where('name', 'LIKE', "%{$query}%") // Case-insensitive in MySQL by default
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->latest()
            ->take(10) // Limit results to prevent UI lag
            ->get(['id', 'name', 'phone']);// Optimize: select only needed cols

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
