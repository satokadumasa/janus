<?php
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CustomSessionCookie;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['csrf-cookie' => 'set']);
});

# Operators
Route::middleware('guest')->post('/user_login', [UserAuthController::class, 'store']);
# admins
Route::middleware('guest')->post('/admin_login', [AdminAuthController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    # Operators
    Route::get('/user', [UserAuthController::class, 'user']);
    Route::post('/user_logout', [UserAuthController::class, 'destroy']);
});
Route::middleware('auth:admin')->group(function () {
    # admins
    Route::get('/admin', [AdminAuthController::class, 'admin']);
    Route::post('/admin_logout', [AdminAuthController::class, 'destroy']);
});
