<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['proudct_id', 'product_attr_id', 'user_id', 'user_type', 'qty'];
}
