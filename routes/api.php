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
Route::post('loginAdmin', 'App\Http\Controllers\AuthController@loginAdmin');
Route::post('registerAdmin', 'App\Http\Controllers\AuthController@registerAdmin');


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
    Route::get('profileAdmin', 'App\Http\Controllers\AuthController@indexAdmin');
    Route::post('profileAdmin/{user}', 'App\Http\Controllers\AuthController@updateAdmin');
    Route::delete('profileAdmin/{user}', 'App\Http\Controllers\AuthController@destroyAdmin');
    
    Route::get('norekAdmin', 'App\Http\Controllers\NomorRekeningController@indexAdmin');

  
    Route::post('storeAdmin/{user}', 'App\Http\Controllers\NomorRekeningController@storeAdmin');  
});