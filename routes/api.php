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

Route::middleware(['auth:sanctum', 'ability:web'])->group(function(){
    Route::get('nomorRekening', 'App\Http\Controllers\NomorRekeningController@index');
    Route::post('nomorRekening', 'App\Http\Controllers\NomorRekeningController@store');

});


Route::middleware(['auth:sanctum', 'ability:admin'])->group(function(){

});