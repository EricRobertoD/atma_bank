<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Auth::routes(['verify' => true]);

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::get('email-verification', 'App\Http\Controllers\AuthController@verify')->name('verification.verify');
Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('logout', 'App\Http\Controllers\AuthController@logout');

Route::middleware(['auth:sanctum', 'ability:web'])->group(function(){
    Route::get('nomorRekening', 'App\Http\Controllers\NomorRekeningController@index');
    Route::post('nomorRekening', 'App\Http\Controllers\NomorRekeningController@store');
    Route::delete('nomorRekening/{nomorRekening}', 'App\Http\Controllers\NomorRekeningController@destroy');

    Route::get('profile', 'App\Http\Controllers\AuthController@index');
    Route::post('profile', 'App\Http\Controllers\AuthController@update');

    Route::post('deposit/{nomorRekening}', 'App\Http\Controllers\TransaksiController@deposit');
    Route::post('transfer/{nomorRekening}', 'App\Http\Controllers\TransaksiController@transfer');
    Route::get('transaksi', 'App\Http\Controllers\TransaksiController@index');
});


Route::middleware(['auth:sanctum', 'ability:admin'])->group(function(){

});