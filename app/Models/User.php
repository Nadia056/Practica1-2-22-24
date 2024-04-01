<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role_id', 'phone', 'verification_code', 'admin_code','is_verified'];
    use HasFactory,HasApiTokens, Notifiable, HasApiTokens;

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }
    
    
    protected $expiresIn = 60;
   
  
    
}

