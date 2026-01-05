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
  ];
}
