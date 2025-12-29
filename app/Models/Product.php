<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = [
    'title',
    'slug',
    'category_id',
    'short_description',
    'description',
    'price',
    'sale_price',
    'status',
  ];

  public function category(){
    return $this->belongsTo(Category::class);
  }

}
