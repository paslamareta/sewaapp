<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    // Halaman utama Inertia (render ke Vue)
    public function index()
    {
        return Inertia::render('users/list');
    }

    // ✅ Endpoint API untuk datatable
    public function apiIndex(Request $request)
    {
        $query = User::query();

        // Searching
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortField = $request->sortField ?? 'name';
        $sortOrder = $request->sortOrder ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $skip = (int) ($request->skip ?? 0);
        $take = (int) ($request->take ?? 10);

        $total = $query->count();
        $items = $query->skip($skip)->take($take)->get();

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }

    // ✅ Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // ✅ Update data
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

         if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // // versi password hash
        // if (!empty($validated['password'])) {
        //     $validated['password'] = Hash::make($validated['password']);
        // } else {
        //     unset($validated['password']);
        // }

        $user->update($validated);

        return response()->json($user);
    }

    // ✅ Hapus data
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
