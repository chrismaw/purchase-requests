<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    public $timestamps = false;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
//        'email_verified_at' => 'datetime',
    ];

    public $dates = [
        'created_at',
        'updated_at',
    ];

    public function isAdmin(){
        return !!$this->is_admin;
    }

    public function isApprover(){
        return !!$this->approver;
    }

    public function isBuyer(){
        return !!$this->buyer;
    }

    public static function current() {
        if(Auth::check()) {
            return User::find(Auth::user()->id);
        } else {
            return null;
        }
    }

}
