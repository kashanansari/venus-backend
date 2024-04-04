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
Route::get('getproperty',[admincontroller::class,'getproperty'])->name('getproperty');
Route::get('single_property/{property_id}',[admincontroller::class,'single_property'])->name('single_property');
Route::put('update_property',[admincontroller::class,'update_property'])->name('update_property');
Route::delete('delete_property/{property_id}',[admincontroller::class,'delete_property'])->name('delete_property');
Route::get('get_active_properties',[admincontroller::class,'get_active_properties'])->name('get_active_properties');
Route::get('upcoming_properties',[admincontroller::class,'upcoming_properties'])->name('upcoming_properties');
Route::get('closed_properties',[admincontroller::class,'closed_properties'])->name('closed_properties');
Route::post('create_vote',[admincontroller::class,'create_vote'])->name('create_vote');
Route::get('get_voting_poll',[admincontroller::class,'get_voting_poll'])->name('get_voting_poll');
Route::put('update_votes',[admincontroller::class,'update_votes'])->name('update_votes');
Route::delete('/{vote_id}',[admincontroller::class,'delete_votes'])->name('delete_votes');
Route::post('/create_news',[admincontroller::class,'create_news'])->name('create_news');
Route::get('/get_news',[admincontroller::class,'get_news'])->name('get_news');
Route::put('/update_news',[admincontroller::class,'update_news'])->name('update_news');


Route::get('time',[usercontroller::class,'time'])->name('time');
