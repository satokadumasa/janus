<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\PersonalAccessToken;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['store']]);
    }
    /**
     * Summary of user
     * @param \Illuminate\Http\Request $request
     */
    public function admin(Request $request) 
    {
        \Log::debug("AdminAuthController::admin() START");
        return response()->json($request->user());
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return array{token: mixed}
     */
    public function store(Request $request)
    {
        \Log::debug("AdminAuthController::store()");
        $request->validate([
            'username' => 'required|max:255',
            'password' => 'required',
        ]);

        \Log::debug("AdminAuthController::store() request:" . print_r($request->all(), true));
        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        Auth::guard('admin')->login($admin);
        \Log::debug("AdminAuthController::store() END");
        return [
            'token' => $admin->createToken('api-token')->plainTextToken
        ];
    }

    public function destroy(Request $request)
    {
        // \Log::debug("AdminAuthController::destroy() START");
        // // $user = Auth::guard('admin')->user();
        // $user = Auth::user();
        // $user->tokens()->delete();
        // \Log::debug("AdminAuthController::destroy() END");
        \Log::debug("AdminAuthController::destroy() START");
        $user = Auth::guard('admin')->user();
        $user->tokens()->delete();
        $request->session()->invalidate();
        \Log::debug("AdminAuthController::destroy() END");
    }
}
