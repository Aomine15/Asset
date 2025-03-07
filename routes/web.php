<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LogAuthentication;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Log\Logger;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', function () {
    return view('Profile.profile');
});


Route::controller(LogAuthentication::class)->group(function(){

    Route::get('/login', 'Login')->name('Login');
    Route::get('/register', 'Register')->name('Register');
    Route::get('/forgot_password', 'Forgot_password')->name('Forgot');

    Route::post('/register', 'RegisterAuth')->name('Registered');
    Route::post('/login', 'LoginAuth')->name('LoginVerified');

});


Route::controller((GoogleController::class))->group(function(){

    Route::get('/auth/google', 'RedirectToGoogle');
    Route::get('/auth/google/callback', 'handleGoogleCallBack');

});

