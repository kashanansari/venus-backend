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
use Illuminate\Foundation\Auth\User as Authenticatable;


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
use App\Models\UserVotes;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class admincontroller extends Controller
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
                'role'=>"Admin",
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
    public function verifyotp(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|exists:users,email',
            'otp'=>'required|numeric'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(), 
            ], 402); 
        }
       $user= User::where('email',$request->email)
        ->first();
        if($user->otp==$request->otp){
            $user->update(['otp'=>null]);
            $token=$user->createToken($user->email)->plainTextToken;
            return response()->json([
                'success' => true,
                'message' =>'Successfull authentication',
                'data'=>$user,
                'token'=>$token 
            ], 402); 

        }
        else{
            return response()->json([
                'success' => false,
                'message' =>'Invalid otp code',
                'data'=>null
            ], 402); 
        }

    }
    public function resetpassword(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|exists:users,email',
            'new_password' => ['required', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required'],            
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
            $user->update(['password'=>Hash::make($request->new_password)]);
            return response()->json([
                'success' => true,
                'message' =>'Password changed successfully',
                'data'=>$user
            ], 402); 
        }
        else{
            return response()->json([
                'success' => false,
                'message' =>'Error in updation',
                'data'=>null
            ], 402); 
        }
    }
    public function change_password(Request $request){
        $auth=Auth::user();
        if($auth){
        $validator=Validator::make($request->all(),[
            'old_password'=>['required','min:8'],
            'new_password' => ['required', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required'],            
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' =>$validator->errors(), 
            ], 402); 
        }
        $user=User::where('id',$auth->id)
        ->first();
        if($user && Hash::check($request->old_password,$user->password)){
            $user->update(['password'=>Hash::make($request->new_password)]);
            return response()->json([
                'success' => true,
                'message' =>'Password changed successfully', 
            ], 200); 
        }
        else{
            return response()->json([
                'success' => false,
                'message' =>'Invalid old password', 
            ], 400);   
        }
    }
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' =>'User logged out successfully', 
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' =>'User not found', 
        ], 404);
    }
        public function createproperty(Request $request){
            $auth=Auth::user();
            Db::beginTransaction();
            try{
            $validator=validator::make($request->all(),[
                // 'user_id'=>'required|exists:users,id',
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
                'gross'=>'required',
                'zoning'=>'nullable',
                'floor_area'=>'required',
                'total_raised_amount'=>'required',
                'attachment'=>'required|file|mimes:pdf,doc,docx|max:2048'

            ]);
            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'message' =>$validator->errors(), 
                ], 402);  
            }
            $propertyExists = Property::where('property_address', $request->property_address)
            ->where('end_date', '>=',$request->end_date)
            ->first();

        if ($propertyExists) {
            return response()->json([
                'success' => false,
                'message' => 'A property with the same address is already active', 
            ], 400);  
        }
            $file = $request->file('attachment')->store('documents', 'public');
            $property=Property::create([
                'user_id'=>$auth->id,
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
                'total_raised_amount'=>$request->total_raised_amount,
                'attachment' => $file,
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
public function getproperty(Request $request){
    Auth::user();
    $property= Property::active()->get();

    if($property){
         return response()->json([
            'success' => true,
            'message' =>'Properties found successfully',
            'data'=>$property
        ], 200);   
    }
    else{
        return response()->json([
            'success' => false,
            'message' =>'Properties not found ',
            'data'=>null
        ], 200);   
    }
}
public function detailproperties(Request $request)
{
    $properties = Property::select('*', DB::raw('(SELECT COUNT(*) FROM investments WHERE property_id = properties.id) as No_of_users_invested'))
    ->get()
    ->map(function ($details) {
        $details->listing_date = $details->created_at->format('d-m-Y');
        unset($details->created_at);
        unset($details->updated_at);
        return $details;
    });


    return response()->json([
        'success' => true,
        'message' => 'Properties found successfully',
        'data' => $properties
    ], 200);
}
public function allproperties(Request $request){
    $properties = Property::select('id as property_id','images','property_address as address','dividend','start_date','end_date','cap','annual_recurring_revenue', DB::raw('(SELECT COUNT(*) FROM investments WHERE property_id = properties.id) as No_of_users_invested'))
    ->get()
    ->map(function ($details) {
        unset($details->created_at);
        unset($details->updated_at);
        return $details;
    });


    return response()->json([
        'success' => true,
        'message' => 'Properties found successfully',
        'data' => $properties
    ], 200);
}


public function single_property($property_id){
    $property=Property::where('id',$property_id)
    ->first();
    if(!$property){
        return response()->json([
            'success' => false,
            'message' =>'Property not found ',
            'data'=>null
        ], 400); 
    }
    else{
        return response()->json([
            'success' => true,
            'message' =>'Property found successfully',
            'data'=>$property
        ], 200); 
    }
}
public function update_property(Request $request){
    $validator=Validator::make($request->all(),[
        'property_id'=>'required|exists:properties,id'
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' =>$validator->errors()
        ], 200); 
    }
    $property=Property::where('id',$request->property_id)
    ->first();
    Db::beginTransaction();
    try{
    if($property){
        $property->images=$request->images;
        $property->property_name=$request->property_name;
        $property->property_type=$request->property_type;
        $property->property_size=$request->property_size;
        $property->rental_price=$request->rental_price;
        $property->rental_frequency=$request->rental_frequency;
        $property->no_of_bedrooms=$request->no_of_bedrooms;
        $property->amenities=$request->amenities;
        $property->description=$request->description;
        $property->verification_details=$request->verification_details;
        $property->property_address=$request->property_address;
        $property->project_completion_date=$request->project_completion_date;
        $property->floor=$request->floor;
        $property->govt_assessed_land=$request->govt_assessed_land;
        $property->cap=$request->cap;
        $property->annual_recurring_avenue=$request->annual_recurring_avenue;
        $property->dividend=$request->dividend;
        $property->declaration=$request->declaration;
        $property->buider_wallet_address=$request->buider_wallet_address;
        $property->min_amount=$request->min_amount;
        $property->max_amount=$request->max_amount;
        $property->start_date=$request->start_date;
        $property->end_date=$request->end_date;
        $property->images=$request->images;
        $property->status="active";
        $property->update();
        Db::commit();
        return response()->json([
            'success' => true,
            'message' =>'property updated successfully',
            'data'=>$property
        ], 200); 
    }
}

catch(\Exception $e){
Db::rollBack();
 return response()->json([
            'success' => false,
            'message' =>'property not updated',
            'error'=>$e->getMessage()
        ], 400);
}

}
public function delete_property($property_id){
    $property = Property::findOrFail($property_id);
    if($property){
        $property->delete();
        return response()->json([
            'success' => true,
            'message' =>'property soft deleted successfully',
            'data'=>$property
        ], 200);
    }
}
public function get_active_properties(Request $request){
    $currentdate=carbon::now()->format('d-m-Y');
    $property=Property::where('start_date','<=',$currentdate)
    ->where('end_date','>=',$currentdate)
    ->get();
    if($property->isEmpty()){
        return response()->json([
            'success' => false,
            'message' =>'No active properties found',
            'data'=>null
        ], 400);
    }
    else{
        return response()->json([
            'success' => true,
            'message' =>'Active properties found successfully',
            'data'=>$property,
            // 'date'=>$currentdate
        ], 200);
    }
}
public function upcoming_properties(Request $request){
    $currentdate=carbon::now()->format('d-m-Y');
    $property=Property::where('start_date','>',$currentdate)
    ->where('end_date','>',$currentdate)
    ->get();
    if($property->isEmpty()){
        return response()->json([
            'success' => false,
            'message' =>'No upcoming properties found',
            'data'=>null
        ], 400);
    }
    else{
        return response()->json([
            'success' => true,
            'message' =>'Upcoming properties found successfully',
            'data'=>$property,
        ], 200);
    }
}
public function closed_properties(Request $request){
    $currentdate=carbon::now()->format('d-m-Y');
    $property=Property::where('end_date','<',$currentdate)
    ->get();
    if($property->isEmpty()){
        return response()->json([
            'success' => false,
            'message' =>'No closed properties found',
            'data'=>null
        ], 400);
    }
    else{
        return response()->json([
            'success' => true,
            'message' =>'Closed properties found successfully',
            'data'=>$property,
        ], 200);
    }
}

public function create_vote(Request $request){
    $auth=Auth::user();
    $validator=Validator::make($request->all(),[
        'user_id'=>'required|exists:users,id',
        'property_id'=>'required|exists:properties,id',
        'title'=>'required',
        'description'=>'required',
        'start_date'=>'required',
        'end_date'=>'required',
        'polling_option'=>'required|in:YES/NO,Accept/Reject',
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' =>$validator->errors(),
        ], 402);    
    }
    $check=Votes::where('property_id',$request->property_id)
    ->where('end_date','>=',formatDate())
    ->first();
    if($check){
        return response()->json([
            'success' => false,
            'message' =>'voting poll of this property already exists',
            'data'=>null
        ], 401);    
    }
    
    Db::beginTransaction();
    try{
    $votes=Votes::create([
        'user_id'=>$auth->id,
        'property_id'=>$request->property_id,
        'title'=>$request->title,
        'description'=>$request->description,
        'start_date'=>$request->start_date,
        'end_date'=>$request->end_date,
        'polling_option'=>$request->polling_option,
        'date'=>formatDate(),
        'time'=>formatTime(),
        'status'=>'opened'
    ]);
    if($votes){
        Db::commit();
        return response()->json([
            'success' => true,
            'message' =>'Voting poll created successfully',
            'data'=>$votes
        ], 200);
    }

    }
    catch(\Exception $e){
        Db::rollBack();
        return response()->json([
            'success' => false,
            'message' =>'Internal server error',
            'data'=>$e->getMessage()
        ], 200);
    }
}
public function get_voting_poll(){
    Votes::where('end_date','<',formatDate())
        ->update(['status'=>'closed']);

    // Fetch all active votes
    $votes = Votes::active()->get();

    // Check if any votes exist
    if($votes->isEmpty()){
        return response()->json([
            'success' => false,
            'message' =>'No voting poll created yet',
            'data'=>null
        ], 400);
    }

    $voteCounts = [];

    foreach ($votes as $vote) {
        $voteCount = UserVotes::where('vote_id', $vote->id)->count();
      $yescount= UserVotes::where('vote_id',$vote->id)
        ->where('vote_choice','=','YES')
        ->count() ;
        $nocount= UserVotes::where('vote_id',$vote->id)
        ->where('vote_choice','=','NO')
        ->count() ;

        $voteCounts[$vote->id] =[ 
            'total_counts'=>$voteCount,
            'yes_counts'=>$yescount,
            'no_counts'=>$nocount
    ];
    }

    return response()->json([
        'success' => true,
        'message' =>'Voting polls found successfully',
        'data' => [
            'votes' => $votes,
            'vote_counts' => $voteCounts
        ]
    ], 200);
}

public function update_votes(Request $request){
    $validator=Validator::make($request->all(),[
        'vote_id'=>'required|exists:votes,id'
    ]);
    if($validator->fails()){
        return response()->json([
            'success' => false,
            'message' =>$validator->errors()
        ], 200); 
    }
    $votes=Votes::where('id',$request->vote_id)
    ->first();
    Db::beginTransaction();
    try{
    if($votes){
        $votes->property_id =$request->property_id ;
        $votes->title=$request->title;
        $votes->description=$request->description;
        $votes->start_date=$request->start_date;
        $votes->end_date=$request->end_date;
        $votes->polling_option=$request->polling_option;
        $votes->date=formatDate();
        $votes->time=formatTime();
        $votes->status="active";
        $votes->update();
        Db::commit();
        return response()->json([
            'success' => true,
            'message' =>'Votes updated successfully',
            'data'=>$votes
        ], 200); 
    }
}

catch(\Exception $e){
Db::rollBack();
 return response()->json([
            'success' => false,
            'message' =>'votes not updated',
            'error'=>$e->getMessage()
        ], 400);
}

}
public function delete_votes($vote_id){
    $votes = Votes::findOrFail($vote_id);
    if($votes){
        $votes->delete();
        return response()->json([
            'success' => true,
            'message' =>'Votes soft deleted successfully',
            'data'=>$votes
        ], 200);
}
else{
    return response()->json([
        'success' => false,
        'message' =>'votes not found ',
        'data'=>null
    ], 400);
}
}
public function create_news(Request $request){
    $auth=Auth::user();
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        // 'user_id' => 'required',
        'description' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif', 
    ]);

    if($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 402);
    }

    $imagePath = $request->file('image')->store('images', 'public');

    Db::beginTransaction();
    try {
        $news = News::create([
            'user_id' => $auth->id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'date'=>formatDate(),
            'time'=>formatTime(),
            'status'=>'active'
        ]);
        Db::commit();

        if($news) {
            return response()->json([
                'success' => true,
                'message' => 'News created successfully',
                'data' => $news
            ], 200);
        }
    } catch(\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error in creation',
            'data' => $e->getMessage()
        ], 500);
    }
}
public function allnews(Request $request){
    // Set default per page value
    $perPage = 10;

    // Check if per_page parameter is provided and is a whole number
    if ($request->filled('per_page') && ctype_digit($request->per_page)) {
        $perPage = intval($request->per_page);
    }

    // Fetch paginated news
    $news = News::active()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

    if(!$news->isEmpty()){
        return response()->json([
            'success' => true,
            'message' => 'News found successfully',
            'data' => $news
        ], 200);
    }
    else{
        return response()->json([
            'success' => false,
            'message' => 'News not found',
            'data' => null
        ], 404);
    }
}


public function update_news(Request $request){
    $validator = Validator::make($request->all(), [
        'news_id' => 'required|exists:news,id',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4048', // Validate image file
    ]);

    if($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 402);
    }

    $news = News::find($request->news_id);

    if(!$news) {
        return response()->json([
            'success' => false,
            'message' => 'News not found',
        ], 404);
    }

    Db::beginTransaction();

    try {
        $imagePath = $request->file('image')->store('images', 'public');

        $news->title = $request->title;
        $news->description = $request->description;
        $news->image = $imagePath;
        $news->date = formatDate();
        $news->time = formatTime();
        $news->status = "active";
        $news->save();

        Db::commit();

        return response()->json([
            'success' => true,
            'message' =>'News updated successfully',
            'data' => $news
        ], 200); 
    } catch(\Exception $e) {
        Db::rollBack();
        return response()->json([
            'success' => false,
            'message' =>'News not updated',
            'error' => $e->getMessage()
        ], 400);
    }
}
public function delete_news($news_id){
    $news=News::findorFail($news_id);
  if($news){
    $news->delete();
    return response()->json([
        'success' => true,
        'message' =>'News deleted successfully',
        'error' =>$news
    ], 400);
  }
  else{
    return response()->json([
        'success' => false,
        'message' =>'Internal server error',
        'data'=>null
    ], 400);
  }
}

public function hash_password(){
    $pass=formatDate();
    
    return $pass;
}

}