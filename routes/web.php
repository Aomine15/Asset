<?php

use App\Http\Controllers\SocialController;
use App\Http\Controllers\LogAuthentication;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;


Route::controller(LogAuthentication::class)->group(function(){

    Route::get('/', 'Login')->name('Login');
    Route::get('/register', 'Register')->name('Register');
    Route::get('/forgot_password', 'Forgot_password')->name('Forgot');

    Route::post('/register', 'RegisterAuth')->name('Registered');
    Route::post('/', 'LoginAuth')->name('LoginVerified');

    Route::get('/logout', 'Logout')->name('logout');

});


Route::controller((SocialController::class))->group(function(){

    // Google Auth
    Route::get('/auth/google', 'RedirectToGoogle');
    Route::get('/auth/google/callback', 'handleGoogleCallBack');

    // Facebook Auth
    Route::get('auth/facebook', 'redirectToFacebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallBack');

});


Route::controller(PagesController::class)->group(function(){

    Route::get('/dashboard', 'Dashboard')
    ->middleware('login-auth')
    ->name('dashboard');

    Route::post('/users', 'AddUser');

    Route::get('/users', 'ViewUser')->name('users');
    Route::get('/profile', 'Profile')->name('profile');


});
