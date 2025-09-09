<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\SewaAplikasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('customers', function () {
    return Inertia::render('customers/list');
})->middleware(['auth', 'verified'])->name('customers');

Route::get('applications', function () {
    return Inertia::render('applications/list');
})->middleware(['auth', 'verified'])->name('applications');

Route::get('hostings', function () {
    return Inertia::render('hostings/list');
})->middleware(['auth', 'verified'])->name('hostings');

Route::get('sewa', function () {
    return Inertia::render('sewa/list');
})->middleware(['auth', 'verified'])->name('sewa');
Route::get('/sewa', [SewaAplikasiController::class, 'apiIndex']);
Route::post('/sewa', [SewaAplikasiController::class, 'store']);
Route::put('/sewa/{id}', [SewaAplikasiController::class, 'update']);
Route::delete('/sewa/{id}', [SewaAplikasiController::class, 'destroy']);

Route::get('users', function () {
    return Inertia::render('users/list');
})->middleware(['auth', 'verified'])->name('users');

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);


Route::get('broadcasts', function () {
    return Inertia::render('broadcasts/list'); 
})->middleware(['auth', 'verified'])->name('broadcasts');

Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('api.broadcasts.index');
    Route::post('/broadcasts', [BroadcastController::class, 'send'])->name('api.broadcasts.send');
    Route::get('/customers/all', [CustomerController::class, 'allCustomers']);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
