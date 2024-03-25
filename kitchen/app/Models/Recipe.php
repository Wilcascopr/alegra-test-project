<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'name',
        'description',
    ];

    public function ingredients()
    {
        return $this->hasManyThrough(Ingredient::class, 'recipe_ingredients', 'recipe_id', 'id', 'id', 'ingredient_id');
    }
}
