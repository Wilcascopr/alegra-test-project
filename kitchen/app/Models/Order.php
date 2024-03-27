<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = ['status'];

    public function items()
    {
        return $this->belongsToMany(Recipe::class, 'order_items', 'order_id', 'recipe_id')->withPivot('quantity', 'order_id', 'recipe_id');
    }
}
