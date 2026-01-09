<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'user_id',
    'name',
    'email',
    'mobile',
    'address',
    'city',
    'state',
    'pin_code',
    'payment_type',
    'payment_status',
    'total_amount',
    'order_status',
    'payment_id',
  ];

  // public function users(){
  //   return $this->belongTo(User::class);
  // }

      public function user()
    {
        return $this->belongsTo(User::class);
    }


}

 
