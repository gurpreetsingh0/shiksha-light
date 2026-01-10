<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
  protected $fillable = [
    'product_id',
    'catalog_number',
    'sku',
    'cct',
    'wattage',
    'body_color',
    'voltage',
    'dimension',
    'material',
    'color',
    'weight',
    'outer_dia',
    'inner_cut',
    'mrp',
    'price',
    'stock',
    'image',
    'status',
    'height',
    'outer_diameter',
    'inner_diameter'
  ];
// Variant.php
public function bodyColor()
{
    return $this->belongsTo(Color::class, 'body_color');
}



}
