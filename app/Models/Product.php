<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function vendor(){
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
