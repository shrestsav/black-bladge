<?php

namespace App;

use App\Notifications\OTPNotification;
use App\Notifications\SystemNotification;
use App\Notifications\AppNotification;
use App\Traits\NotificationLogics;
use App\Order;
use Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use NotificationLogics;
    use HasApiTokens, Notifiable;
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'username',
        'password',
        'phone',
        'OTP',
        'OTP_timestamp',
        'photo',
        'gender',
        'country',
        'dob',
        'license_no',
        'last_login'
    ];

    protected $appends = ['full_name','photo_src'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->phone;
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('phone', $username)->orWhere('username',$username)->first();
    }

    /**
    * Validate the password of the user for the Passport password grant.
    *
    * @param  string $password
    * @return bool
    */
    public function validateForPassportPasswordGrant($OTP)
    {
        //check if user is customer or driver first
        if($this->hasRole('customer')){
            // return Hash::check($password, $this->password);
            $OTP_timestamp = \Carbon\Carbon::parse($this->OTP_timestamp);
            $current = \Carbon\Carbon::now();
            $totalTime = \Carbon\Carbon::now()->diffInMinutes($OTP_timestamp);

            if($this->OTP==$OTP && $totalTime<=config('settings.OTP_expiry')) return true;
        }
        elseif($this->hasRole('driver')){
            return Hash::check($OTP, $this->getAuthPassword());
        }

        return false;
    }
    public function orders()
    {
        return $this->hasMany(Order::class,'customer_id');
    }

    public function driverList()
    {
        $drivers = $this->whereHas('roles', function ($query) {
                          $query->where('name', '=', 'driver');
                       })
                        ->with('details')
                        ->orderBy('id','DESC')
                        ->get();

        return $drivers;
    }

    public function adminList()
    {
        $superAdmin = $this->whereHas('roles', function ($query) {
                          $query->where('name', '=', 'superAdmin');
                       });

        return $superAdmin->get();
    }

    public function customerList()
    {
        $customers = $this->whereHas('roles', function ($query) {
                        $query->where('name', '=', 'customer');
                     })->whereNotNull('fname')->whereNotNull('lname');

        return $customers->get();
    }

    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }

    public function getPhotoSrcAttribute()
    {
        return $this->photo ? asset('files/users/'.$this->id.'/'.$this->photo) : asset('/files/images/person.png');
    }

    public function sendOTP()
    {
        $OTP = $this->OTP;
        $this->notify(new OTPNotification($OTP));
    }

    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function goCash()
    {
        return $this->hasOne(GoCash::class);
    }
    /**
     * Send Push Notifications
     *
     * @param  array  $notification
     * @return boolean
     */
    public function pushNotification($notification)
    {   
        $this->notify(new SystemNotification($notification));
    }

    /**
     * Store App Notifications on Database
     *
     * @param  array  $notification
     * @return boolean
     */
    public function AppNotification($notification)
    {   
        $this->notify(new AppNotification($notification));
    }

    public function tok()
    {
        // foreach($userTokens as $token) {
        //     $token->revoke();   
        // }
        // return $userTokens;

        $collection = collect([
            'tokens' => $this->tokens,
            'active' => Auth::user()->token()
        ]);

        return $collection;
    }

    public function activeBooking()
    {
        $bookings = $this->orders()->whereIn('status',[0,1])->first();
        
        if($bookings)
            return true;
        else 
            return false;
    }

}
