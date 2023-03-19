<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api'])->group(function ($router){
    Route::post('/register','App\Http\Controllers\RegisterController@register');
    Route::post('/login','App\Http\Controllers\LoginController@login');
    Route::get('email/verify/{id}','App\Http\Controllers\VerificationController@verify')->name('verification.verify');
    Route::get('email/resesnd','App\Http\Controllers\VerificationController@resend')->name('verification.resend');
    Route::post('/forgot-password','App\Http\Controllers\PasswordResetController@sendemail');
    // Route::get('/reset/password/{token}','App\Http\Controllers\PasswordResetController@resetform');
    Route::post('/reset-password','App\Http\Controllers\PasswordResetController@passwordreset')->name('password.reset');
});

Route::middleware(['jwt.auth'])->group(function (){
    Route::post('logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
});