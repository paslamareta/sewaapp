<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Requests\ApplicationRequest;

class ApplicationController extends Controller
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

        $query = Application::query();

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
    public function store(ApplicationRequest $request)
    {

        $application = Application::create($request->validated());
        return response()->json([
            'message' => 'Application created successfully',
            'data' => $application
        ], 201);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $application = Application::find($request->id);
        return response()->json($application, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRequest $request)
    {
        $application = Application::findOrFail($request->id);
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        $application->update($request->validated());
        return response()->json([
            'message' => 'Application updated successfully',
        ]);
    }
    public function destroy(Request $request)
    {
        $application = Application::findOrFail($request->id);
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        $application->delete();
        return response()->json([
            'message' => 'Application deleted successfully',
        ]);
    }
}
