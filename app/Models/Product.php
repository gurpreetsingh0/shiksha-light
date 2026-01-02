<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = [
    'title',
    'slug',
    'wattage',
    'category_id',
    'short_description',
    'description',
    'price',
    'sale_price',
    'status',
    'image',
    'is_featured',
    'is_discounted',
    'is_tranding'
  ];

  public function category(){
    return $this->belongsTo(Category::class);
  }

}
