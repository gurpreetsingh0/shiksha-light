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

  #One Product has many gallery images
  public function gallary_images()
  {
    return $this->hasMany(ProductImage::class);
  }
  public function variants()
  {
    return $this->hasMany(Variant::class)->with('bodyColor');
  }

  

}
