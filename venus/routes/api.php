<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\buildercontroller;
use App\Http\Controllers\admincontroller;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//user
Route::post('create_user_kyc',[usercontroller::class,'create_user_kyc'])->name('create_user_kyc');
Route::put('kyc_accept',[usercontroller::class,'kyc_accept'])->name('kyc_accept');
Route::put('kyc_reject',[usercontroller::class,'kyc_reject'])->name('kyc_reject');
//builder

Route::post('create_builder_kyc',[buildercontroller::class,'create_builder_kyc'])->name('create_builder_kyc');
Route::put('kyc_accept',[buildercontroller::class,'kyc_accept'])->name('kyc_accept');
Route::put('kyc_reject',[buildercontroller::class,'kyc_reject'])->name('kyc_reject');
//admin
Route::post('createproperty',[admincontroller::class,'createproperty'])->name('createproperty');

Route::get('time',[usercontroller::class,'time'])->name('time');
