<?php
// app/Models/BusinessAuthModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class BusinessAuthModel extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'businesses';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'company_name',
        'remember_token',
        'profile_img',
        'is_active',
        'is_paid',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
