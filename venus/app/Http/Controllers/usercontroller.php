<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\RequestOptions;

use Google\Cloud\Translate\V2\TranslateClient;

use Illuminate\Http\Response;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;


use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\dateTraits;
use Illuminate\Support\Facades\Validator;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Http;


// use Auth;
use Illuminate\Support\Facades\Hash;
use DateTime;
use PDF;
use Illuminate\Http\Client\RequestException;
use App\DateTimehelper\formatDate; 
use  App\DateTimehelper\formatTime;
use App\Models\Builder_kyc;
use App\Models\Dividend;
use App\Models\Investment;
use App\Models\News;
use App\Models\Property;
use App\Models\Shares;
use App\Models\Transfer;
use App\Models\User_kyc;
use App\Models\UserVotes;
use App\Models\User;
use App\Models\Votes;
use App\Models\Withdraw;
use App\Models\News_views;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class usercontroller extends Controller
{
    //
    public function signup(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => ['required'],
                'last_name' => ['required'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'min:8', 'confirmed'],
                'password_confirmation' => ['required'],
                'phone_no'=>'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $otpCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

  $image=$request->file('image')->store('userimages','public');

            DB::beginTransaction();
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_no'=>$request->phone_no,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image'=>$image,
                'otp'=>$otpCode,
                'role'=>"User",
                'status'=>"inactive",
            ];
            // $token=$data->createToken($request->email)->plainTextToken;
            try {
                $users = User::create($data);
                Mail::to($users->email)->send(new OtpMail($otpCode));

                DB::commit();
            }
             catch (\Throwable $e) {
                DB::rollback();
                echo $e->getMessage();
                $users = null;
            }
            if ($users != null) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'An otp code sent to your email please check',
                    ],200);
            } else {
                return response()->json(
                    [
                        'message' => 'Internal server error',
                        'success' => false,
                        'users' => null,

                    ],
                    500
                );
            }

    }
    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email',
            'password'=>'required'

        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(), 
            ], 402);  
        }
        $user=User::where('email',$request->email)
        ->first();
        if($user){
            $password=Hash::check($request->password,$user->password);
        if($password){
            $token=$user->createToken($user->email)->plainTextToken;
            return response()->json([
                'success' => true,
                'message' =>'User logged in successfully',
                'data'=>$user,
                'token'=>$token
            ], 200);  
        }
        else{
            return response()->json([
                'success' => false,
                'message' =>'Invalid credentials',
                'data'=>null 
            ], 400);  
        }

        }
        return response()->json([
            'success' => false,
            'message' =>'Internal server error',
            'data'=>null 
        ], 500);  
    }
    public function connect_wallet(Request $request){
        $auth=Auth::user();
        $validator=Validator::make($request->all(),[
            'wallet_address'=>'required|unique:users,wallet_address'
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->messages()
            ], 402, );
        }
        DB::beginTransaction();
        try{
        $wallet=User::where('id',$auth->id)
        ->update(['wallet_address'=>$request->wallet_address]);
        if($wallet){
            DB::commit();
            return response()->json([
                'success'=>true,
                'message'=>'Wallet address added successfully',
                'data'=>''
            ], 200, );
        }

        }
        catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage()
            ], 200, );
    }
    }
    public function create_user_kyc(Request $request){
       $auth=Auth::user();
        DB::beginTransaction();

        try {
        $validator=Validator::make($request->all(),[
        //  'user_id'=>'required|exists:users,id',
         'date_of_birth'=>'required',
         'cnic'=>'required',
         'license'=>'required',
         'passport'=>'required',
         'yearly_tax_report'=>'required',
         'nationality'=>'required',
         'res_address'=>'required',
         'street_address'=>'required',
         'city'=>'required',
         'state'=>'required',
         'postal_code'=>'required',
         'country'=>'required',
         'additional_info'=>'required',
         'occupation'=>'required',
         'source_of_funds'=>'required',
        //  'date'=>'required',
        //  'time'=>'required|exists:users,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->messages()
            ], 500, );
        }

        
        $user_kyc=User_kyc::create([
            
                'user_id'=>$auth->id,
                'date_of_birth'=>$request->date_of_birth,
                'cnic'=>$request->cnic,
                'licenese'=>$request->licenese,
                'passport'=>$request->passport,
                'yearly_tax_report'=>$request->yearly_tax_report,
                'nationality'=>$request->nationality,
                'res_address'=>$request->res_address,
                'street_address'=>$request->street_address,
                'city'=>$request->city,
                'state'=>$request->state,
                'postal_code'=>$request->postal_code,
                'additional_info'=>$request->additional_info,
                'country'=>$request->country,
                'occupation'=>$request->occupation,
                'source_of_funds'=>$request->source_of_funds,
                'date'=>formatDate(),
                'time'=>formatTime(),
                'status'=>'pending'
                
            
        ]);
        if($user_kyc)
        {
            DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'data submitted successfully',
                'data'=>$user_kyc
            ], 500, );
        }
        
        else{
            
            return response()->json([
                'success'=>false,
                'message'=>'data not submitted ',
                'data'=>null
            ], 500, );
        }

    }
    catch (\Exception $e) {
        // Rollback the transaction if an exception occurs
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing the request',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function time(){
        $time=formatTime();
        if($time){
        return response()->json([
            'success'=>true,
            'time'=>$time
        ], 500, );
    }
}
    public function kyc_accept(Request $request){
        Auth::user();
        $validator=Validator::make($request->all(),[
            'kyc_id'=>'required|exists:user_kycs,id'

        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors() 
            ], 500);     
           }
           $user_kyc=User_kyc::where('id',$request->kyc_id)
           ->first();
           if($user_kyc){
            $user_kyc->update(['status'=>'accept']);
            return response()->json([
                'success' => true,
                'message' =>'Status updated successfully', 
            ], 200);   

           }
           else{
            return response()->json([
                'success' => false,
                'message' =>'Status not updated', 
            ], 500); 
           }
    }
    public function kyc_reject(Request $request){
        $validator=Validator::make($request->all(),[
            'kyc_id'=>'required|exists:user_kycs,id'

        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors() 
            ], 500);     
           }
           $user_kyc=User_kyc::where('id',$request->kyc_id)
           ->first();
           if($user_kyc){
            $user_kyc->update(['status'=>'reject']);
            return response()->json([
                'success' => true,
                'message' =>'Status updated successfully', 
            ], 200);   

           }
           else{
            return response()->json([
                'success' => false,
                'message' =>'Status not updated', 
            ], 500); 
           }
    }
    public function create_user_vote(Request $request){
        $auth=Auth::user();
        $validator=Validator::make($request->all(),[
            'vote_id'=>'required|exists:votes,id',
            // 'user_id'=>'required|exists:users,id',
            // 'admin_id'=>'required|exists:users,id',
            'vote_choice'=>'required|in:YES,NO,Accept,Reject',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(), 
            ], 402); 
        }
        $exists=UserVotes::where('vote_id',$request->vote_id)
        ->where('user_id',$request->user_id)
        ->exists();
if($exists){
    return response()->json([
        'success' => false,
        'message' =>'You have already cast your vote', 
        'data'=>null
    ], 402); 
}
        Db::beginTransaction();
        try{
            $admin_id = Votes::where('id',$request->vote_id)
            ->pluck('user_id')
            ->first();
           
            
       $user_votes= UserVotes::create([
            'user_id'=>$auth->id,
            'vote_id'=>$request->vote_id,
            'admin_id'=>$admin_id,
            'vote_choice'=>$request->vote_choice,
            'date'=>formatDate(),
            'time'=>formatTime(),
            'status'=>'active'
        ]);
        if($user_votes){
            Db::commit();
            return response()->json([
                'success' => true,
                'message' =>'User cast vote successfully',
                'data'=>$user_votes 
            ], 200); 
        }
    }
    catch(\Exception $e){
        Db::rollBack();
        return response()->json([
            'success' => false,
            'message' =>'Error in creation',
            'data'=>$e->getMessage()
        ], 500); 
    }
    }
    public function create_investment(Request $request){
        $auth=Auth::user();
        $validator=Validator::make($request->all(),[
        'property_id'=>'required|exists:properties,id',
        'wallet_address'=>'required',
        'invested_amount'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(),
            ], 402);
        }
       $existing_investment= Investment::where('wallet_address',$request->wallet_address)
        ->where('property_id',$request->property_id)
        ->first();
        if($existing_investment){
            $new_amount=$existing_investment->invested_amount+$request->invested_amount;
            $existing_investment->update(['invested_amount'=>$new_amount]);
            return response()->json([
                'success' => true,
                'message' =>'Investment updated successfully',
                'data'=>$existing_investment,
            ], 200);
        }
        else{
        try{
            Db::beginTransaction();
        $investment=Investment::create([
            'user_id'=>$auth->id,
            'property_id'=>$request->property_id,
            'wallet_address'=>$request->wallet_address,
            'invested_amount'=>$request->invested_amount,
            'invested_date'=>formatDate(),
            'invested_time'=>formatTime(),
    
        ]);
        if($investment){
            DB::commit();
            return response()->json([
                'success' => true,
                'message' =>'Investment created successfully',
                'data'=>$investment
            ], 200);
        }
    }
    catch(\Exception $e){
        return response()->json([
            'success' => false,
            'error' =>$e->getMessage(),
        ], 400);
    }
    }
    
    }
    public function get_amount_for_withdarwl($property_id){
        $auth=Auth::user();
      
        $data=Investment::where('property_id',$property_id)
        ->where('user_id',$auth->id)
        ->select('invested_amount','invested_date')
        ->get();
        if($data->isEmpty()){
            return response()->json([
                'success' => false,
                'message' =>'No data found',
                'data'=>null
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' =>'Data found succcessfully',
            'data'=>$data
        ], 200);

    }
public function withdraw(Request $request){
    $auth=Auth::user();
        $validator=Validator::make($request->all(),[
    'property_id'=>'required|exists:properties,id',
    'amount'=>'required'
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'error' =>$validator->errorS(),
        ], 400);
    }
    $exists=Property::where('id',$request->property_id)
    ->where('end_date','>=',formatDate())
    ->first();
    if($exists){
        return response()->json([
            'success' => false,
            'error' =>'Cannot with draw untill the pool ends',
        ], 400);
    }
    DB::beginTransaction();
    try{
        Investment::where('property_id',$request->property_id)
                   ->where('user_id',$auth->id) 
                   ->delete();
    $withdarw=Withdraw::create([
        'user_id'=>$auth->id,
        'property_id'=>$request->property_id,
        'amount'=>$request->amount,
        'date'=>formatDate(),
        'time'=>formatTime(),
    ]);
    if($withdarw){
        Db::commit();
        return response()->json([
            'success' => true,
            'error' =>'Amount withdrawl successfully',
        ], 200);
    }
    
}
catch(\Exception $e){
    return response()->json([
        'success' => false,
        'error' =>$e->getMessage(),
    ], 200);
}
}
public function transfer(Request $request){
 $validator=Validator::make($request->all(),[
      'property_id'=>'required',
      'reciever_id'=>'required',

 ]) ;  
}

public function view_on_news(Request $request)
{
    $auth = Auth::user();
    $validator = Validator::make($request->all(), [
        'news_id' => 'required|exists:news,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 402);
    }

    $already = News_views::where('news_id', $request->news_id)
        ->where('user_id', $auth->id)
        ->exists();

    if ($already) {
        return response()->json([
            'success' => false,
            'message' => 'Already seen this news earlier',
        ], 400);
    }

    DB::beginTransaction();
    try {
        $news_views = News_views::create([
            'news_id' => $request->news_id,
            'user_id' => $auth->id
        ]);

        if ($news_views) {
            $views = News_views::where('news_id', $request->news_id)->count();
            $update_news = News::where('id',$request->news_id)->first(); // Find the news item
            $update_news->total_views = $views; // Update total_views with the count of views
            $update_news->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Views added successfully',
                'data' => $news_views,
                // 'count'=>$views,
            ], 200);
        }
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 400);
    }
}


}