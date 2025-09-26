<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
// use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PersonalAccessToken;

class TokenAuthenticationController extends Controller
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
        \Log::debug("TokenAuthenticationController::user() START");
        return response()->json($request->user());
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return array{token: mixed}
     */
    public function store(Request $request)
    {
        \Log::debug("TokenAuthenticationController::store()");
        $request->validate([
            'username' => 'required|max:255',
            'password' => 'required',
            'extension' => [
                'required',
            ]
        ]);
        \Log::debug("TokenAuthenticationController::store() request:" . print_r($request->all(), true));
        $user = User::where('username', $request->username)->first();
        \Log::debug("TokenAuthenticationController::store() user:" . print_r($user, true));

        if (!$user || !Hash::check($request->password, $user->password)) {
            \Log::debug("TokenAuthenticationController::store() CH-03");
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        \Log::debug("TokenAuthenticationController::store() CH-04");
        Auth::login($user);
        \Log::debug("TokenAuthenticationController::store() END");
        return [
            'token' => $user->createToken('token-name')->plainTextToken
        ];
    }

    public function destroy(Request $request)
    {
        \Log::debug("TokenAuthenticationController::destroy() START");
        $user = Auth::user();
        $user->tokens()->delete();
        \Log::debug("TokenAuthenticationController::destroy() END");
    }
}
