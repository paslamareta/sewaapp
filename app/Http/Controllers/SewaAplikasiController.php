<?php

namespace App\Http\Controllers;

use App\Models\SewaAplikasi;
use App\Models\Customer;
use App\Models\Application;
use App\Models\Hosting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SewaAplikasiController extends Controller
{
    // Untuk halaman utama Inertia
    public function index()
    {
        return Inertia::render('sewa/list');
    }

    // Endpoint API untuk datatable
    public function apiIndex(Request $request)
    {
        $query = SewaAplikasi::with(['customer', 'application', 'hosting']);

        // optional: filter search
        if ($request->search) {
            $query->where('domain', 'like', '%' . $request->search . '%');
        }

        $total = $query->count();
        $items = $query
            ->skip($request->skip ?? 0)
            ->take($request->take ?? 10)
            ->get();

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }

    // Dropdown API
    public function dropdowns()
    {
        return response()->json([
            'customers'    => Customer::all(),
            'applications' => Application::all(),
            'hostings'     => Hosting::all(),
        ]);
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'application_id'   => 'required|exists:applications,id',
            'hosting_id'       => 'required|exists:hostings,id',
            'domain'           => 'required|string|max:255',
            'biaya'            => 'required|numeric',
            'tanggal_mulai'    => 'required|date',
            'tanggal_expired'  => 'required|date',
            'status'           => 'required|string',
        ]);

        $sewa = SewaAplikasi::create($validated);

        return response()->json($sewa, 201);
    }

    // Update data
    public function update(Request $request, $id)
    {
        $sewa = SewaAplikasi::findOrFail($id);

        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'application_id'   => 'required|exists:applications,id',
            'hosting_id'       => 'required|exists:hostings,id',
            'domain'           => 'required|string|max:255',
            'biaya'            => 'required|numeric',
            'tanggal_mulai'    => 'required|date',
            'tanggal_expired'  => 'required|date',
            'status'           => 'required|string',
        ]);

        $sewa->update($validated);

        return response()->json($sewa);
    }

    // Hapus data
    public function destroy($id)
    {
        $sewa = SewaAplikasi::findOrFail($id);
        $sewa->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
