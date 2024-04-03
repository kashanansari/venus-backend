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
use App\Models\User;
use App\Models\Votes;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;


class admincontroller extends Controller
{
    //
    public function createproperty(Request $request){
        Db::beginTransaction();
        try{
        $validator=validator::make($request->all(),[
            'user_id'=>'required|exists:users,id',
            'images' => 'required|image|mimes:png,jpeg',
            'property_name'=>'required',
            'property_type'=>'required',
            'property_size'=>'required',
            'rental_price'=>'required',
            'rental_frequency'=>'required',
            'no_of_bedrooms'=>'required',
            'amenities'=>'required',
            'description'=>'required',
            'verification_details'=>'required',
            'property_address'=>'required',
            'project_completion_date'=>'required',
            'floor'=>'required',
            'govt_assessed_land'=>'required',
            'cap'=>'required',
            'annual_recurring_avenue'=>'required',
            'dividend'=>'required',
            'declaration'=>'required',
            'buider_wallet_address'=>'required',
            'min_amount'=>'required',
            'max_amount'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(), 
            ], 402);  
        }
        $property=Property::create([
            'user_id'=>$request->user_id,
            'images'=>$request->images,
            'property_name'=>$request->property_name,
            'property_type'=>$request->property_type,
            'property_size'=>$request->property_size,
            'rental_price'=>$request->rental_price,
            'rental_frequency'=>$request->rental_frequency,
            'no_of_bedrooms'=>$request->no_of_bedrooms,
            'amenities'=>$request->amenities,
            'description'=>$request->description,
            'verification_details'=>$request->verification_details,
            'property_address'=>$request->property_address,
            'project_completion_date'=>$request->project_completion_date,
            'floor'=>$request->floor,
            'govt_assessed_land'=>$request->govt_assessed_land,
            'cap'=>$request->cap,
            'annual_recurring_avenue'=>$request->annual_recurring_avenue,
            'dividend'=>$request->dividend,
            'declaration'=>$request->declaration,
            'buider_wallet_address'=>$request->buider_wallet_address,
            'min_amount'=>$request->min_amount,
            'max_amount'=>$request->max_amount,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'status'=>"active",
            
        ]);
        if($property){
            Db::commit();
            return response()->json([
                'success' => true,
                'message' =>'Property created successfully',
                'data'=>$property 
            ], 200);   
        }
        else{
            return response()->json([
                'success' => false,
                'message' =>'Property not created',
                'data'=>null 
            ], 400);   
        }
    }
    catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing the request',
            'error' => $e->getMessage()
        ], 500);
    }

}
}