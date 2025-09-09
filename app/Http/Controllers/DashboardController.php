<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Application;
use App\Models\SewaAplikasi;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'customers'    => Customer::count(),
                'applications' => Application::count(),
                // pastikan status sesuai isi database, misalnya "aktif" bukan "active"
                'activeSewa'   => SewaAplikasi::where('status', 'aktif')->count(),
                'expiredSewa'  => SewaAplikasi::whereDate('tanggal_expired', '<', Carbon::today())->count(),
            ],

            // data expired dengan relasi customer + application
            'expiredSewaData' => SewaAplikasi::with(['customer', 'application'])
                ->whereDate('tanggal_expired', '<', Carbon::today())
                ->get(),

            // jumlah sewa per aplikasi
            // 'sewaPerApp' => Application::withCount('sewa')->get(),
            'sewaPerApp' => Customer::with(['sewaAplikasi.application'])->get(),
        ]);
    }
}