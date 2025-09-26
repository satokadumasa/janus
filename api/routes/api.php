<?php
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\PartnerAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CustomSessionCookie;

# Operators
Route::middleware('guest')->post('/user_login', [UserAuthController::class, 'store']);
# partners
Route::middleware('guest')->post('/partner_login', [PartnerAuthController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    # Operators
    Route::get('/user', [UserAuthController::class, 'user']);
    Route::post('/user_logout', [UserAuthController::class, 'destroy']);
});
Route::middleware('auth:partner')->group(function () {
    # partners
    Route::get('/partner', [PartnerAuthController::class, 'partner']);
    Route::post('/partner_logout', [PartnerAuthController::class, 'destroy']);
});
