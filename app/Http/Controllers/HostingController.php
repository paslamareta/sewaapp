<?php

namespace App\Http\Controllers;

use App\Models\Hosting;
use Illuminate\Http\Request;

class HostingController extends Controller
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

        $query = Hosting::query();

        // Filter pencarian (di nama_hosting atau url)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_hosting', 'LIKE', "%{$search}%")
                  ->orWhere('url', 'LIKE', "%{$search}%");
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_hosting' => 'required|string|max:255',
            'url' => 'required|url',
            'active' => 'required|boolean',
        ]);

        Hosting::create($validated);

        return response()->json([
            'message' => 'Hosting created successfully',
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $hosting = Hosting::find($request->id);

        return response()->json($hosting, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_hosting' => 'required|string|max:255',
            'url' => 'required|url',
            'active' => 'required|boolean',
        ]);

        $hosting = Hosting::findOrFail($request->id);
        $hosting->update($validated);

        return response()->json([
            'message' => 'Hosting updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $hosting = Hosting::findOrFail($request->id);
        $hosting->delete();

        return response()->json([
            'message' => 'Hosting deleted successfully',
        ]);
    }
}
