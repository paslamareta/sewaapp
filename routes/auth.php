<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\SewaAplikasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
Route::middleware('auth')->group(function () {
    Route::get('api/applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    Route::post('api/applications', [ApplicationController::class, 'store'])
        ->name('applications.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('api/applications/edit/{application}', [ApplicationController::class, 'edit'])
        ->name('applications.edit');
    Route::put('api/applications/{id}', [ApplicationController::class, 'update'])
        ->name('applications.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('api/applications/{id}', [ApplicationController::class, 'destroy'])
        ->name('applications.destroy');

    Route::get('api/application-hosts', [HostController::class, 'index'])
        ->name('hosts.index');
    Route::get('api/application-hosts/{id}', [HostController::class, 'findById'])
        ->name('hosts.findById');

    Route::get('api/customers', [CustomerController::class, 'index'])
        ->name('customers.index');
    Route::post('api/customers', [CustomerController::class, 'store'])
        ->name('customers.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('api/customers/edit/{customer}', [CustomerController::class, 'edit'])
        ->name('customers.edit');
    Route::put('api/customers/{id}', [CustomerController::class, 'update'])
        ->name('customers.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('api/customers/{id}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy');

    Route::get('api/hostings', [HostingController::class, 'index'])
        ->name('hostings.index');
    Route::post('api/hostings', [HostingController::class, 'store'])
        ->name('hostings.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('api/hostings/edit/{hosting}', [HostingController::class, 'edit'])
        ->name('hostings.edit');
    Route::put('api/hostings/{id}', [HostingController::class, 'update'])
        ->name('hostings.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('api/hostings/{id}', [HostingController::class, 'destroy'])
        ->name('hostings.destroy');

    Route::get('sewa', [SewaAplikasiController::class, 'index'])->name('sewa.index');
    Route::get('api/sewa', [SewaAplikasiController::class, 'apiIndex'])->name('sewa.api.index');
    Route::post('api/sewa', [SewaAplikasiController::class, 'store'])->name('sewa.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::put('api/sewa/{id}', [SewaAplikasiController::class, 'update'])->name('sewa.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('api/sewa/{id}', [SewaAplikasiController::class, 'destroy'])->name('sewa.destroy');
    
    
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('api/users', [UserController::class, 'apiIndex'])->name('users.api.index');
    Route::post('api/users', [UserController::class, 'store'])->name('users.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::put('api/users/{id}', [UserController::class, 'update'])->name('users.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('api/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

});
Route::get('/token', function () {
    $token = csrf_token();
    return response()->json([
        'csrf_token' => $token,
    ])
        ->withCookie('XSRF-TOKEN', $token);
});
