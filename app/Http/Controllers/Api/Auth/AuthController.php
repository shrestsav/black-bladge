<?php

namespace App\Http\Controllers\Api\Auth;

use Auth;
use Mail;

use App\Role;
use App\User;
use App\AppDefault;
use App\UserDetail;
use App\DeviceToken;
use App\Mail\notifyMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Hash;
use App\Notifications\OTPNotification;
use Illuminate\Support\Facades\Validator;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Api\Driver\Driver as DriverResource;
use App\Http\Resources\Api\Customer\Customer as CustomerResource;



class AuthController extends Controller
{
    public function phoneRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
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
        Mail::send(new notifyMail($tempMailData));

        //If Exists login otherwise register as new customer
        if($check->exists()){
            $check->update([
                        'OTP' => $request['OTP'],
                        'OTP_timestamp' => $request['OTP_timestamp']
                    ]);

            // $customer = $check->first()->sendOTP();

            return response()->json([
                'message'     => trans('response.OTP_sent'),
                'user_status' => 'existing',
            ]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'phone'=> 'unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => trans('response.validation_failed'),
                    'errors' => $validator->errors(),
                ], 422);
            }
            $customer = User::create($request->all());
            $role_id = Role::where('name','customer')->first()->id;

            //Assign User as Customer
            $customer->attachRole($role_id);

            // $customer->sendOTP();

            return response()->json([
                'message'     => trans('response.OTP_sent'),
                'user_status' => 'new',
                // 'code'        =>$request['OTP']
            ]);
        }
    }

    public function verifyOTP(Request $request)
    {   
        $client_secret = \DB::table('oauth_clients')->where('id',2)->first()->secret;

        $appDefaults = AppDefault::firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'phone'         => 'required',
            'OTP'           => 'required|numeric',
            'device_id'     => 'required',
            'device_token'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('phone',$request->phone);
        
        if(!$user->exists()){
            return response()->json([
                'message' => "Validation Error",
                'errors' => [
                    'phone' =>  ['Phone number doesnot exists']
                ],
            ], 422);
        }
        
        $user = $user->first();

        //Check OTP
        if(!$user->validateForPassportPasswordGrant($request->OTP)){
            return response()->json([
                'message' => "Validation Error",
                'errors' => [
                    'OTP' =>  ['OTP is invalid or expired, try resending OTP code.']
                ],
            ], 422);
        }

        $user_details = false;
        $url = url('').'/oauth/token';

        $user_id = $user->id;
        $role = $user->roles()->first()->name;
        $details = $user->fname;
        if($details)
            $user_details = true;
        
        $http = new \GuzzleHttp\Client();
        $response = $http->post(url('').'/oauth/token', [
                        'form_params' => [
                            'grant_type' => 'password',
                            'client_id' => 2,
                            'client_secret' => $client_secret,
                            'username' => $request->phone,
                            'password' => $request->OTP,
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
                'user_id' => $user_id
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
            'user_details'  =>  $user_details,
            'configs'       =>  [
                'pricing_unit'  =>  'AED',
                'cost_per_km'   =>  $appDefaults->cost_per_km,
                'cost_per_min'  =>  $appDefaults->cost_per_min,
            ]
        ];

        return response()->json($result);
    }

    public function driverLogin(Request $request)
    {   
        $appDefaults = AppDefault::firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string|max:191|exists:users,username',
            'password'     => 'required|max:100',
            'device_id'    => 'required|string|max:191',
            'device_token' => 'required|string|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('username',$request->username)->firstOrFail();

        if (!Hash::check($request->password, $user->getAuthPassword())) {
            return response()->json([
                'status' => '422',
                'message' => trans('response.validation_failed'),
                'errors' => [
                    'password' => ['Your password is incorrect']
                ],
            ], 422);
        }

        $client_secret = \DB::table('oauth_clients')->where('id',2)->first()->secret;

        $url = url('').'/oauth/token';
        
        $user_id = $user->id;
        $role = $user->roles()->first()->name;
        
        $http = new \GuzzleHttp\Client();
        $response = $http->post(url('').'/oauth/token', [
                        'form_params'       => [
                            'grant_type'    => 'password',
                            'client_id'     => 2,
                            'client_secret' => $client_secret,
                            'username'      => $request->username,
                            'password'      => $request->password,
                            'scope'         => '',
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
            'tokens'    =>  $token_response,
            'role'      =>  $role,
            'user'      =>  new DriverResource($user),
            'configs'   =>  [
                'pricing_unit'  =>  'AED',
                'cost_per_km'   =>  $appDefaults->cost_per_km,
                'cost_per_min'  =>  $appDefaults->cost_per_min,
            ]
        ];

        return response()->json($result);
    }

    public function checkRole()
    {
        $role = Auth::user()->roles()->first()->name;

        return response()->json([
            'user_id' => Auth::id(),
            'role' => $role
        ]);
    }

    public function tokens()
    {
        return User::find(Auth::id())->tok();
    }

    public function test()
    {
        $address = [
            [
                'name' => 'bharatpur',
                'location' => '19.123.4324.123.1234',
                'Block' => 'D'
            ],
            [
                'name' => 'Chitwan',
                'location' => '66.112.432.123.132',
                'Block' => 'E'
            ],
        ];
        UserDetail::where('id',1)->update(['address' => json_encode($address,true)]);
        return json_decode(UserDetail::find(1)->address);
        return url('').'/oauth/token';
        // return 'something';
        $http = new \GuzzleHttp\Client();
        // $request = $http->get('google.com');
        // return $request->getBody();
        $response = $http->post('http://go.rinse/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => 2,
                'client_secret' => 'wNfEEpIS6VUHVZKVhgUWGNjcNTUvkd5gGtsnCgnb',
                'username' => '+9779808224917',
                'password' => '3067',
                'scope' => '',
            ],
        ]);

        // return $response;
        return json_decode((string) $response->getBody(), true);




        // $getUnregisteredUser = User::where('id',1)->first();
        // $accessToken = $getUnregisteredUser->createToken('manual token');
        // $accessToken = $getUnregisteredUser->createToken('manual token')->accessToken;
        // return $accessToken;
    }


    public function notifications()
    {
        $user = User::find(Auth::id());

        //First Mark All Notifications as read
        $user->unreadNotifications()->update(['read_at' => now()]);

        // Return notifications 
        return response()->json($user->notifications->take(50));
    }

    public function countUnreadNotifications()
    {
        return response()->json(User::find(Auth::id())->unreadNotifications->count());
    }


    public function markAsRead($notificationId)
    {
        $user = User::find(Auth::id());

        $notification = $user->unreadNotifications->find($notificationId);
        if($notification){
            $notification->markAsRead();
            return response()->json([
                'status' => '200',
                'message'=>'Notifications Marked as read'
            ],200);
        }
        return response()->json([
            'status' => '404',
            'message'=>'Notification Not Found'
        ],404);
    }

    public function markAllAsRead()
    {
        $user = User::find(Auth::id());

        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'status' => '200',
            'message'=>'All Notifications Marked as read'
        ],200);
    }

    public function removeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        DeviceToken::where('device_token',$request->device_token)->delete();

        return response()->json([
            'status' => '200',
            'message'=>'Device Token Removed'
        ],200);
    }

}
