<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const VERIFIED_USER = 1;
    const UNVERIFIED_USER =0;

    const ADMIN_USER = 1;
    const REGULAR_USER = 0;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVerified()
    {
        return $this->verified == self::VERIFIED_USER;
    }

    public function isAdmin()
    {
        return $this->admin == self::ADMIN_USER;
    }

    public static function generateVerificationCode()
    {
        return Str::random(40);
    }

    public function getNameAttribute()
    {
        return ucwords($this->name);
    }

    /*Mutators*/
    public function setNameAttribute(string $name)
    {
        $this->attributes['name'] = strtolower($name);
    }

    public function setEmailAttribute(string $email)
    {
        $this->attributed['email'] = strtolower($email);
    }
}
