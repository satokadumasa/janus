<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Adnin;
use App\Models\PersonalAccessToken;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:partner', ['except' => ['store']]);
    }
    /**
     * Summary of user
     * @param \Illuminate\Http\Request $request
     */
    public function partner(Request $request) 
    {
        \Log::debug("AdminAuthController::partner() START");
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
        $partner = Admin::where('username', $request->login_email)->first();

        if (!$partner || !Hash::check($request->password, $partner->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        Auth::guard('admin')->login($partner);
        \Log::debug("AdminAuthController::store() END");
        return [
            'token' => $partner->createToken('api-token')->plainTextToken
        ];
    }

    public function destroy(Request $request)
    {
        \Log::debug("AdminAuthController::destroy() START");
        // $user = Auth::guard('partner')->user();
        $user = Auth::user();
        $user->tokens()->delete();
        \Log::debug("AdminAuthController::destroy() END");
    }
}
