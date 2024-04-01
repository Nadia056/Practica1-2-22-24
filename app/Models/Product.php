<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'category_id', 'status'];
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $attributes = [
        'status' => 'active'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
