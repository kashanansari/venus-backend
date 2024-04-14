<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Builder_kyc;
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
use App\Models\Dividend;
use App\Models\Investment;
use App\Models\News;
use App\Models\Property;
use App\Models\Shares;
use App\Models\Transfer;
use App\Models\User_kyc;
use App\Models\User;
use App\Models\Votes;
use App\Models\UserVotes;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
class buildercontroller extends Controller
{
    //
    public function buildersignup(Request $request){
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
                'role'=>"Builder",
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
    public function builderlogin(Request $request){
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
    public function create_builder_kyc(Request $request){
     $auth=Auth::user();
        DB::beginTransaction();

        try {
        $validator=Validator::make($request->all(),[
        //  'builder_id'=>'required|exists:users,id',
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
     
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->messages()
            ], 500, );
        }

        
        $builder_kyc=Builder_kyc::create([
            
                'builder_id'=>$auth->id,
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
        if($builder_kyc)
        {
            DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'data submitted successfully',
                'data'=>$builder_kyc
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
public function kyc_accept(Request $request){
    $validator=Validator::make($request->all(),[
        'kyc_id'=>'required|exists:builder_kycs,id'

    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors() 
        ], 500);     
       }
       $builder_kyc=Builder_kyc::where('id',$request->kyc_id)
       ->first();
       if($builder_kyc){
        $builder_kyc->update(['status'=>'accept']);
        return response()->json([
            'success' => true,
            'message' =>'Status updated successfully', 
            'data'=>$builder_kyc, 
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
        'kyc_id'=>'required|exists:builder_kycs,id'

    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors() 
        ], 500);     
       }
       $builder_kyc=Builder_kyc::where('id',$request->kyc_id)
       ->first();
       if($builder_kyc){
        $builder_kyc->update(['status'=>'reject']);
        return response()->json([
            'success' => true,
            'message' =>'Status updated successfully',
            'data'=>$builder_kyc, 
        ], 200);   

       }
       else{
        return response()->json([
            'success' => false,
            'message' =>'Status not updated', 
        ], 500); 
       }
}

public function dividend(Request $request){
    $validator = Validator::make($request->all(), [
        'property_id' => 'required|exists:properties,id'
    ]);

    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' => $validator->errors() 
        ], 402); // Change status code to 400 for bad request     
    }
 $exists=Dividend::where('property_id',$request->property_id)
 ->first();
 if($exists){
    return response()->json([
        'success' => false,
        'message' =>'Dividend amouunt already distributed with the investors' 
    ], 400);
 }
    $dividend = Property::where('id', $request->property_id)
        ->select('dividend')
        ->exists();
        if($dividend){

        }

    if(!$dividend) {
        return response()->json([
            'success' => false,
            'message' => 'Property not found' 
        ], 404); // Change status code to 404 for not found
    }

    // Retrieve investments and their respective users
    $investments = Investment::where('property_id', $request->property_id)
        ->join('users', 'investments.user_id', '=', 'users.id')
        ->select('users.id', 'users.email', 'users.wallet_address', 'investments.invested_amount', 'investments.invested_date')
        ->get();

    // Calculate total investment
    $total_investment = $investments->sum('invested_amount');

    // Calculate dividend amount per invested amount percentage
    $dividend_per_percentage = $dividend->dividend / $total_investment;

    // Calculate dividend amount for each investment
    foreach ($investments as $investment) {
        $investment->dividend_amount = $investment->invested_amount * $dividend_per_percentage;
        Dividend::create([
            'user_id'=>$investment->id,
            'property_id'=>$request->property_id,
            'amount'=>$investment->dividend_amount,
            'date'=>formatDate(),
            'time'=>formatTime(),
            ]);
    }
   


    return response()->json([
        'success' => true,
        'data' => [
            'dividend' => $dividend->dividend,
            'investments' => $investments,
            'total_investment' => $total_investment,
            
        ]
    ], 200);
}
public function profile(Request $request){
    $auth = Auth::user();
    if($auth){
        $user = User::where('id', $auth->id)
                   ->select('image', 'first_name', 'last_name', 'email')
                   ->first();
        $property = Property::where('user_id', $auth->id)
                            ->get();
        $propertyWithVotes = [];

        foreach($property as $propertyItem){
            $votes = Votes::where('property_id', $propertyItem->id)
                          ->get();

            // Count occurrences of each vote in UserVotes
            $voteCounts = [];
            $totalyescounts=[];
            $totalnocounts=[];
            foreach ($votes as $vote) {
                $voteId = $vote->id;
                $count = UserVotes::where('vote_id', $voteId)->count();
                $yescount = UserVotes::where('vote_id', $voteId)
                ->where('vote_choice','YES')
                ->count();
                $nocount = UserVotes::where('vote_id', $voteId)
                ->where('vote_choice','NO')
                ->count();           
                     $voteCounts = $count;
                $totalyescounts=$yescount;
                $totalnocounts=$nocount;
            }

            $propertyItem->votes_count = $voteCounts;
            $propertyItem->yes_count = $totalyescounts;
            $propertyItem->no_count = $totalnocounts;
            $propertyWithVotes[] = $propertyItem;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data found successfully',
            'data' => $user,
            'properties' => $propertyWithVotes
        ], 200);
    }
}

                   
                            
                        
                        // return response()->json([
                        //     'success' => true,
                        //     'message' => 'Data found successfully',
                        //     'data'=>$user,
                            

                        // ], 200);


    }
 

