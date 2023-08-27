<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use HasMediaTrait;
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
