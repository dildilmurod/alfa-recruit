<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

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

    public function vacancy(){
        return $this->hasMany('App\Models\Vacancy');
    }

//    function candidate() {
//        return $this->hasMany('App\Models\Candidate', 'user_id', 'id');
//    }

    public function candidates(){
        return $this->belongsToMany('App\Models\Candidate',
            'candidate_user', 'user_id', 'candidate_id' )
            ->withPivot('thumb');
    }









}
