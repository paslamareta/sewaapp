<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $skip = (int) $request->query('skip', 0);
        $take = (int) $request->query('take', 10);
        $search = $request->query('search', '');
        $sortField = $request->query('sortField', 'id');
        $sortOrder = $request->query('sortOrder', 'asc');

        $query = Customer::query();

        // Filter pencarian (di code, name, category)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Hitung total sebelum pagination
        $total = $query->count();

        // Sorting
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $items = $query->skip($skip)
            ->take($take)
            ->get();

        return response()->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {

        $validated = $request->validated(); // ambil data valid

        // tambahkan created_by & updated_by
        $validated['created_by_id']   = $request->user()->id;
        $validated['created_by_name'] = $request->user()->name;
        $validated['updated_by_id']   = $request->user()->id;
        $validated['updated_by_name'] = $request->user()->name;

        Customer::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
        ], 201);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $customer = Customer::find($request->id);
        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request)
    {
        $customer = Customer::findOrFail($request->id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $customer->update($request->validated());
        return response()->json([
            'message' => 'Customer updated successfully',
        ]);
    }
    public function destroy(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $customer->delete();
        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }
    public function allCustomers()
{
    return response()->json([
        'items' => Customer::select('id','name','email')->get()
    ]);
}




}
