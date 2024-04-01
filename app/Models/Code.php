<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;
        //   $table->id();
        //     $table->string('code');
        //     $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        //     $table->boolean('is_verified')->default(false);
        //     $table->string('status')->default('active');
        //     $table->timestamps();
    protected $fillable = ['code', 'user_id', 'is_verified', 'status'];
    protected $table = 'codes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $attributes = [
        'status' => 'active'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
