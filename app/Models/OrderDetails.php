<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $fillable = ['order_id', 'product_id', 'variant_id', 'price', 'qty'];

    
    public function product(){
      return $this->belongsTo(Product::class);
    }

    public function order(){
      return $this->belongsTo(Order::class);
    }

    public function variant(){
      return $this->belongsTo(Variant::class);
    }
 
}
