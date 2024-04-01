<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    //  Schema::create('categories', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('name');
    //         $table->string('status')->default('active');
    //         $table->timestamps();
    //     });
    // }
    protected $fillable = ['name', 'status'];
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $attributes = [
        'status' => 'active'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
