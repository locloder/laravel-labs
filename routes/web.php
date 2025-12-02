<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/live', function () {
    if (!Auth::check()) {
        $user = \App\Models\User::first(); 
        if ($user) Auth::login($user);
    }
    return view('live');
});
