<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Host;

class HostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $hosts = Host::where('name', 'like', "%{$search}%")
            ->get()
            ->map(fn($host) => [
                'label' => $host->name,
                'value' => $host->id
            ]);

        return response()->json($hosts);
    }
    public function findById(Request $request, $id)
    {
        $host = Host::findOrFail($id);
        return response()->json([
            'label' => $host->name,
            'value' => $host->id
        ]);
    }
}
