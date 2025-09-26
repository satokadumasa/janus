<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner;
use App\Models\PersonalAccessToken;

class PartnerAuthController extends Controller
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
        \Log::debug("PartnerAuthController::partner() START");
        return response()->json($request->user());
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return array{token: mixed}
     */
    public function store(Request $request)
    {
        \Log::debug("PartnerAuthController::store()");
        $request->validate([
            'login_email' => 'required|max:255',
            'password' => 'required',
        ]);
        


        \Log::debug("PartnerAuthController::store() request:" . print_r($request->all(), true));
        $partner = Partner::where('login_email', $request->login_email)->first();

        if (!$partner || !Hash::check($request->password, $partner->password)) {
            throw ValidationException::withMessages([
                'login_email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        Auth::guard('partner')->login($partner);
        \Log::debug("PartnerAuthController::store() END");
        return [
            'token' => $partner->createToken('api-token')->plainTextToken
        ];
    }

    public function destroy(Request $request)
    {
        \Log::debug("PartnerAuthController::destroy() START");
        // $user = Auth::guard('partner')->user();
        $user = Auth::user();
        \Log::debug("PartnerAuthController::destroy() partner:" . print_r($user, true));
        $user->tokens()->delete();
        \Log::debug("PartnerAuthController::destroy() END");
    }
}
