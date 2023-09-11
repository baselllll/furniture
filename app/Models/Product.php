<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use HasMediaTrait;
    public $translatable = ['name','brand','type','status','description'];
    protected $fillable =
        [
            'price',
            'category_id',
            'quantity',
            'discount',
            'name',
            'brand',
            'type',
            'status',
            'featured',
            'description'
        ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_products');
    }
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }
}
