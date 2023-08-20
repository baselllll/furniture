<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'price',
            'quantity',
            'discount',
            'name',
            'brand',
            'type',
            'status',
            'featured',
        ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_products');
    }
}
