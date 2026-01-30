<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Fitur Pencarian
        if ($request->has('search')) {
            $q = $request->search;
            $query->where('name', 'LIKE', "%{$q}%")
                ->orWhere('phone', 'LIKE', "%{$q}%");
        }

        // --- INI KUNCI PERBAIKANNYA ---
        // Kita wajib mengambil data berantai:
        // Transactions -> Items -> Product
        $customers = $query->with(['transactions.items.product'])
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    // ... (Biarkan fungsi store, update, destroy tetap seperti kode Anda sebelumnya) ...
    // Pastikan fungsi di bawah ini ada untuk POS:

    public function search(Request $request)
    {
        $q = $request->get('query');
        return response()->json(Customer::where('name', 'LIKE', "%{$q}%")->orWhere('phone', 'LIKE', "%{$q}%")->limit(10)->get());
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
