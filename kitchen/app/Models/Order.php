<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function items()
    {
        return $this->hasManyThrough(Recipe::class, 'order_items', 'order_id', 'id', 'id', 'recipe_id')->withPivot('quantity');
    }
}
