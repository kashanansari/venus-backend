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
use App\Models\Poperty;
use App\Models\Shares;
use App\Models\Transfer;
use App\Models\User_kyc;
use App\Models\User;
use App\Models\Votes;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
class buildercontroller extends Controller
{
    //
    public function create_builder_kyc(Request $request){
        DB::beginTransaction();

        try {
        $validator=Validator::make($request->all(),[
         'builder_id'=>'required|exists:users,id',
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
            
                'builder_id'=>$request->builder_id,
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
        $builder_kyc->update(['status'=>'Accept']);
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
        $builder_kyc->update(['status'=>'Reject']);
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

}
