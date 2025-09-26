<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PersonalAccessToken;

class UserAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['store']]);
    }
    /**
     * Summary of user
     * @param \Illuminate\Http\Request $request
     */
    public function user(Request $request) 
    {
        \Log::debug("UserAuthController::user() START");
        return response()->json($request->user());
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return array{token: mixed}
     */
    public function store(Request $request)
    {
        \Log::debug("UserAuthController::store()");
        $request->validate([
            'username' => 'required|max:255',
            'password' => 'required',
            'extension' => [
                'required',
            ]
        ]);
        \Log::debug("UserAuthController::store() request:" . print_r($request->all(), true));
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        Auth::guard('api')->login($user);
        \Log::debug("UserAuthController::store() END");
        return [
            'token' => $user->createToken('api-token')->plainTextToken
        ];
    }

    public function destroy(Request $request)
    {
        \Log::debug("UserAuthController::destroy() START");
        $user = Auth::user();
        $user->tokens()->delete();
        \Log::debug("UserAuthController::destroy() END");
    }
}
