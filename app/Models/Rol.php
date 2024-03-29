<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = [
        'name',
        'role_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    use HasFactory;
}
