<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    $user = Auth::loginUsingId(1);
    
    $token = $user->createToken('test');

    dd($token);
});
