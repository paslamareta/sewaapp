<?php

use Illuminate\Support\Facades\Route;
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


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
