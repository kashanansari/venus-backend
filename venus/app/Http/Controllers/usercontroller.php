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
use App\Models\Poperty;
use App\Models\Shares;
use App\Models\Transfer;
use App\Models\User_kyc;
use App\Models\UserVotes;
use App\Models\User;
use App\Models\Votes;
use App\Models\Withdraw;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class usercontroller extends Controller
{
    //
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
        DB::beginTransaction();

        try {
        $validator=Validator::make($request->all(),[
         'user_id'=>'required|exists:users,id',
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
            
                'user_id'=>$request->user_id,
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
                'status'=>'Reject'
                
            
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
            $user_kyc->update(['status'=>'Accept']);
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
            $user_kyc->update(['status'=>'Reject']);
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
        $validator=Validator::make($request->all(),[
            'vote_id'=>'required|exists:votes,id',
            'user_id'=>'required|exists:users,id',
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
            'user_id'=>$request->user_id,
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
}
