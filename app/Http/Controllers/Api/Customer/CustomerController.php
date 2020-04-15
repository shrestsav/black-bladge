<?php

namespace App\Http\Controllers\Api\Customer;

use App\AppDefault;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Order;
use App\User;
use App\UserAddress;
use App\UserDetail;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Validator;
use App\Http\Resources\Api\Customer\Customer as CustomerResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(new CustomerResource(Auth::user()));
    }

    public function createProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname'  => 'required|string',
            'email'  => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
            'gender' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => '422',
                'message' => trans('response.validation_failed'),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userInput = $request->only('fname', 'lname', 'email', 'gender');

        if($request->referred_by){
            $check = UserDetail::where('referral_id',$request->referred_by);
            if(!$check->exists()){
                return response()->json([
                    'status' => '404',
                    'message'=> 'Referral ID is Invalid' 
                ],404);
            }
        }

        $address = User::where('id',Auth::id())->update($userInput);
        
        if($request->email){
            User::notifyNewRegistration(Auth::id());
        }
        
        //Generate random Referral ID for registered user
        $random_string = substr($request->fname, 0, 3).rand(100,999).Str::random(10);
        $referral_id = strtoupper(substr($random_string, 0, 8));
        
        //Save User Photo 
        $userDetail = UserDetail::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'referred_by' => $request->referred_by,
                    'referral_id' => $referral_id
                ]);

        return response()->json([
            'status' => '200',
            'message'=> trans('response.profile_created'), 
        ],200);
    }

    public function updateProfile(Request $request)
    {
        if ($request->fname || $request->lname || $request->gender) {
            $msgs = [
                "fname.required" => "First Name Cannot be empty"
            ];
            $validator = Validator::make($request->all(), [
                "fname"  => ['required', 'string', 'max:255'],
                "gender" => ['required', 'string', 'max:255'],
            ], $msgs);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            $address = User::where('id',Auth::id())->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'gender' => $request->gender
            ]);

            return response()->json([
                'status' => '200',
                'message'=> 'Profile Updated Successfully' 
            ],200);
        }

        if ($request->email) {
            $user = User::findOrFail(Auth::id());
            if($user->email==$request->email){
                return response()->json([
                    'status' => '200',
                    'message'=> 'Same Email Detected' 
                ],200);
            }
            
            $validator = Validator::make($request->all(), [
               'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $input = $request->only('email');
            $address = $user->update($input);

            return response()->json([
                'status' => '200',
                'message'=> 'Email Updated Successfully' 
            ],200);
        }
        
        //Save User Photo 
        if ($request->hasFile('photo')) {
            $validator = Validator::make($request->all(), [
                "photo" => 'mimes:jpeg,jpg,bmp,png|max:15072',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $image = Image::make($request->file('photo'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $photo = $request->file('photo');
            $fileName = 'dp_user_'.Auth::id().'.jpg';
            $uploadDirectory = public_path('files'.DS.'users'.DS.Auth::id());
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);

            $userDetail = User::find(Auth::id())->update([
                'photo' => $fileName
            ]);

            return response()->json([
                'status' => '200',
                'message'=> 'Photo Updated Successfully', 
                'url' => asset('files/users/'.Auth::id().'/'.$fileName)
            ],200);
        } 

        return response()->json([
            'status' => '400',
            'message' => 'No parameters found to complete the request'
        ], 400);
    }

    public function changePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "newPhone" => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $error,
            ], 422);
        }

        $OTP = rand(1000,9999);
        $OTP_timestamp = Date('Y-m-d H:i:s');
        User::where('id',Auth::id())->update([
                        'OTP' => $OTP,
                        'OTP_timestamp' => $OTP_timestamp
                    ]);
        return response()->json([
                'status' => '200',
                'message'=> 'OTP has been sent to your phone', 
                'OTP'=> $OTP, 
            ],200);
    }

    public function updatePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newPhone' => 'required',
            'OTP' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::find(Auth::id());
        $OTP_timestamp = \Carbon\Carbon::parse($user->OTP_timestamp);
        $current = \Carbon\Carbon::now();
        $totalTime = \Carbon\Carbon::now()->diffInMinutes($OTP_timestamp);

        if($user->OTP==$request->OTP && $totalTime<=config('settings.OTP_expiry')){
            User::where('id',Auth::id())->update([
                        'phone' => $request->newPhone,
                    ]);
            return response()->json([
                'status' => '200',
                'message'=> 'Phone Number has been updated successfully', 
            ],200);
        }
        else{
            return response()->json([
                'status' => '403',
                'message'=> 'OTP did not match or may have expired', 
            ],403);
        }
        
    }

    public function getAddress()
    {
        $address = UserAddress::where('user_id',Auth::id())->get();
        return response()->json($address);
    }
    
    public function addAddress(Request $request)
    {
        unset($request['map_coordinates']);

        $validator = Validator::make($request->all(), [
            'area_id' => 'required|numeric',
            'type' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $request['user_id'] = Auth::id(); 

        if($request->lattitude && $request->longitude){
            $coordinates = [
                'lattitude' => $request->lattitude,
                'longitude' => $request->longitude
            ];
            $request['map_coordinates'] = $coordinates;
        }

        //Remove other defaults if exists
        if($request->is_default){
            $lastDefault = UserAddress::where('user_id',Auth::id())
                                      ->where('is_default',1)
                                      ->update([
                                        'is_default' => 0
                                      ]);

        }
        $address = UserAddress::create($request->all());
        return response()->json($address);
    }    

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $coordinates = null; 
        if($request->lattitude && $request->longitude){
            $coordinates = [
                'lattitude' => $request->lattitude,
                'longitude' => $request->longitude
            ];
        }
        $address = UserAddress::where('id',$request->address_id)->where('user_id',Auth::id());
        if($address->exists()){
            $address->update([
                'name' => $request->name,
                'area_id' => $request->area_id,
                'map_coordinates' => $coordinates,
                'building_community' => $request->building_community,
                'type' => $request->type,
                'appartment_no' => $request->appartment_no,
                'remarks' => $request->remarks,
            ]);
            return response()->json([
                'status' => '200',
                'message'=> 'Address Updated Successfully' 
            ],200);
        }
        else{
            return response()->json([
                'status' => '403',
                'message'=> 'Address doesnot exist' 
            ],403);
        }
        
    }

    public function setDefaultAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lastDefault = UserAddress::where('user_id',Auth::id())
                                  ->where('is_default',1)
                                  ->update([
                                    'is_default' => 0
                                  ]);

        $address = UserAddress::where('id',$request->address_id)
                              ->where('user_id',Auth::id())
                              ->firstOrFail()
                              ->update([
                                    'is_default' => 1
                              ]);

        return response()->json([
            'status' => '200',
            'message'=> 'Default Set Successfully' 
        ],200);
    }

    /**
     * Remove the address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAddress($id)
    {
        $check = UserAddress::findOrFail($id);
        $check_if_used = Order::where('pick_location',$id)->orWhere('drop_location',$id);
        if($check->user_id!=Auth::id()){
            return response()->json([
                "status" => "403",
                "message" => "You donot have access to this address"
            ], 403);
        }
        if($check_if_used->exists()){
            return response()->json([
                "status" => "403",
                "message" => "Sorry, You've already used this address."
            ], 403);
        }
        
        $check->delete();
        return response()->json([
            "status" => "200",
            "message" => "User Address has been deleted"
        ], 200);
    }
}
