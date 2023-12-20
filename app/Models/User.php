<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'target',
        'current',
        'remember_token',
        'device_token',
        'role_id',
        'status_id',
        'team_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
//        'password' => 'hashed',
    ];

    public function Leads(): HasMany
    {
        return $this->hasMany(Lead::class ,"sales_id","id");
    }

    public function Role(): HasOne
    {
        return $this->hasOne(Role::class, "id", "role_id");
    }
    public function Team(): HasOne
    {
        return $this->hasOne(Team::class, "id", "team_id");
    }

    public function UserStatues(): HasOne
    {
        return $this->hasOne(UserStatues::class, "id", "status_id");
    }
}
