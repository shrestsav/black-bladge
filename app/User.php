<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname','lname', 'email', 'username', 'password','phone','OTP','OTP_timestamp'
    ];

    /**
     * The attributes that should be append for arrays.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }

    /**
    * Validate the password of the user for the Passport password grant.
    *
    * @param  string $password
    * @return bool
    */
    public function validateForPassportPasswordGrant($OTP)
    {
        // return Hash::check($password, $this->password);
        $OTP_timestamp = \Carbon\Carbon::parse($this->OTP_timestamp);
        $current = \Carbon\Carbon::now();
        $totalTime = \Carbon\Carbon::now()->diffInMinutes($OTP_timestamp);

        if($this->OTP==$OTP && $totalTime<=config('settings.OTP_expiry'))
            return true;

        return false;
    }

    /**
    * Get Full Name of User
    *
    * @param  string 
    * @return string
    */
    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }

    public function sendOTP()
    {
        $OTP = $this->OTP;
        $this->notify(new OTPNotification($OTP));
    }
}
