<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use Auth;

use Mail;
use App\Mail\notifyMail;

class AuthController extends Controller
{
    public function phoneRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => '422',
                'message' => 'Validation Failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // $request['OTP'] = rand(1000,9999);
        $request['OTP'] = 1111;
        $request['OTP_timestamp'] = Date('Y-m-d H:i:s');

        //Check if already exists
        $check = User::where('phone','=',$request->phone);

        //Remove this, just for test
        $phn = $request->phone;

        $tempMailData = [
            'emailType' => 'new_order',
            'name'      => 'Codeilo',
            'email'     => 'codeilo.solutions.pvt@gmail.com',
            'orderID'   => 'OTP',
            'subject'   => 'OTP for ' . $phn . ' is ' . $request['OTP'],
            'message'   => "Please disable this particular email in production"
        ];
        
        // Notify Customer in email
        // Mail::send(new notifyMail($tempMailData));

        //If Exists login otherwise register as new customer
        if($check->exists()){
            $check->update([
                        'OTP' => $request['OTP'],
                        'OTP_timestamp' => $request['OTP_timestamp']
                    ]);

            // $customer = $check->first()->sendOTP();

            return response()->json([
                'message'     => 'OTP has been send to your phone',
                'user_status' => 'existing'
            ]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'phone'=> 'unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $customer = User::create($request->all());
            $customer->assignRole('customer');

            // $customer->sendOTP();

            return response()->json([
                'message'     =>'OTP has been send to your phone',
                'user_status' => 'new',
                // 'code'        =>$request['OTP']
            ]);
        }
    }

    public function verifyOTP(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'phone'         => 'required',
            'OTP'           => 'required|numeric',
            'device_id'     => 'required',
            'device_token'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = '';
        $user_id = '';
        $user_details = false;
        $url = url('').'/oauth/token';
        $OTP = $request->OTP;
        $phone = $request->phone;
        $user = User::where('phone',$phone)->firstOrFail();
        if($user){
            $user_id = $user->id;
            $role = $user->getRoleNames()[0];
            $details = $user->full_name;
            if($details)
                $user_details = true;
        }
        $http = new \GuzzleHttp\Client();
        $response = $http->post(url('').'/oauth/token', [
                        'form_params' => [
                            'grant_type' => 'password',
                            'client_id' => 2,
                            'client_secret' => 'wNfEEpIS6VUHVZKVhgUWGNjcNTUvkd5gGtsnCgnb',
                            'username' => $phone,
                            'password' => $OTP,
                            'scope' => '',
                        ],
                        'http_errors' => true // add this to return errors in json
                    ]);

        $token_response = json_decode((string) $response->getBody(), true);
        
        $check = DeviceToken::where('device_id',$request->device_id)
                            ->where('device_token',$request->device_token);

        //If both device_id and device_token exists                           
        if($check->exists()){
            $check->update([
                'user_id'   =>  $user_id
            ]);
        }

        // If device_id only exists
        else{
            $deviceToken = DeviceToken::updateOrCreate(
                        [
                            'device_id' => $request->device_id
                        ],
                        [
                            'user_id'      => $user_id,
                            'device_token' => $request->device_token
                        ]
                    );
        }

        $result = [
            'tokens'        =>  $token_response,
            'role'          =>  $role,
            'user_id'       =>  $user_id,
            'user_details'  =>  $user_details
        ];

        return response()->json($result);
    }
}
