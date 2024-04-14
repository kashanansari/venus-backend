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
    Route::middleware(['auth:sanctum','User'])->group(function(){

    Route::post('create_user_kyc',[usercontroller::class,'create_user_kyc'])->name('create_user_kyc');
    // Route::put('kyc_accept',[usercontroller::class,'kyc_accept'])->name('kyc_accept');
    // Route::put('kyc_reject',[usercontroller::class,'kyc_reject'])->name('kyc_reject');
    Route::post('create_user_vote',[usercontroller::class,'create_user_vote'])->name('create_user_vote');
    Route::get('get_userid',[usercontroller::class,'get_userid'])->name('get_userid');
    Route::post('/create_investment',[usercontroller::class,'create_investment'])->name('create_investment');
    Route::post('/connect_wallet',[usercontroller::class,'connect_wallet'])->name('connect_wallet');
    Route::get('/get_amount_for_withdarwl/{property_id}',[usercontroller::class,'get_amount_for_withdarwl'])->name('get_amount_for_withdarwl');
    Route::post('/withdraw',[usercontroller::class,'withdraw'])->name('withdraw');
    Route::post('/view_on_news',[usercontroller::class,'view_on_news'])->name('view_on_news');

    });
    //builder
    Route::middleware(['auth:sanctum','Builder'])->group(function(){

    Route::post('create_builder_kyc',[buildercontroller::class,'create_builder_kyc'])->name('create_builder_kyc');
    Route::get('profile',[buildercontroller::class,'profile'])->name('profile');

    // Route::get('dividend',[buildercontroller::class,'dividend'])->name('dividend');
});
    //admin
    Route::middleware(['auth:sanctum','Admin'])->group(function(){

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
    Route::get('/allnews',[admincontroller::class,'allnews'])->name('allnews');
    Route::post('/update_news',[admincontroller::class,'update_news'])->name('update_news');
    Route::delete('/delete_news/{news_id}',[admincontroller::class,'delete_news'])->name('delete_news');
    // Route::post('/create_investment',[admincontroller::class,'create_investment'])->name('create_investment');
    Route::put('kyc_accept',[admincontroller::class,'kyc_accept'])->name('kyc_accept');
    Route::put('kyc_reject',[admincontroller::class,'kyc_reject'])->name('kyc_reject');
    Route::get('/detailproperties',[admincontroller::class,'detailproperties'])->name('detailproperties');

    });
    // Route::post('/signup',[admincontroller::class,'signup'])->name('signup');
    // Route::post('/login',[admincontroller::class,'login'])->name('login');
    // Route::post('/verifyotp',[admincontroller::class,'verifyotp'])->name('verifyotp');
    // Route::post('/resetpassword',[admincontroller::class,'resetpassword'])->name('resetpassword');
    Route::put('/change_password',[admincontroller::class,'change_password'])->name('change_password');
    Route::post('/logout',[admincontroller::class,'logout'])->name('logout');
    // Route::get('/detailproperties',[admincontroller::class,'detailproperties'])->name('detailproperties');
    Route::get('/allproperties',[admincontroller::class,'allproperties'])->name('allproperties');
    Route::get('/propertyinvestmentdetails/{property_id}',[admincontroller::class,'propertyinvestmentdetails'])->name('propertyinvestmentdetails');
    Route::get('/get_builder_kyc',[admincontroller::class,'get_builder_kyc'])->name('get_builder_kyc');
    Route::get('/get_user_kyc',[admincontroller::class,'get_user_kyc'])->name('get_user_kyc');

    
    Route::get('time',[usercontroller::class,'time'])->name('time');
    Route::get('hash_password',[admincontroller::class,'hash_password'])->name('hash_password');

    //Admin,Builder,User authentication
    Route::post('/adminlogin',[admincontroller::class,'adminlogin'])->name('adminlogin');
    Route::post('/userlogin',[usercontroller::class,'userlogin'])->name('userlogin');
    Route::post('/builderlogin',[buildercontroller::class,'builderlogin'])->name('builderlogin');
    Route::post('/usersignup',[usercontroller::class,'usersignup'])->name('usersignup');
    Route::post('/buildersignup',[buildercontroller::class,'buildersignup'])->name('buildersignup');
    Route::post('/verifyotp',[usercontroller::class,'verifyotp'])->name('verifyotp');
    // Route::post('/verifyotp',[buildercontroller::class,'verifyotp'])->name('verifyotp');
    // Route::post('/resetpassword',[usercontroller::class,'resetpassword'])->name('resetpassword');
    Route::post('/resetpassword',[buildercontroller::class,'resetpassword'])->name('resetpassword');
    Route::post('dividend',[buildercontroller::class,'dividend'])->name('dividend');
