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

        $customers = $query->with(['transactions.items.product'])
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function search(Request $request)
    {
        $query = trim($request->get('query'));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $customers = \App\Models\Customer::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->latest()
            ->take(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    public function storeFromPos(Request $request)
    {
        $request->validate(['name' => 'required', 'phone' => 'required']);
        $c = Customer::create($request->all());
        return response()->json(['status' => 'success', 'customer' => $c]);
    }

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
