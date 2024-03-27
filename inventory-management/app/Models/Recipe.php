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
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients', 'recipe_id', 'ingredient_id')
            ->withPivot('quantity', 'recipe_id', 'ingredient_id');
    }
}
