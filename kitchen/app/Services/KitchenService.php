<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class KitchenService
{

    public function createEmptyOrder()
    {
        $order = Order::create(['status' => 'received']);
        return $order->id;
    }

    public function getOrders($page)
    {
        $limit = 20;
        return Order::with('items')->paginate($limit, ['*'], 'page', $page);
    }

    public function getOrder($id)
    {
        return Order::with('items.ingredients')->find($id);
    }

    public function getRecipes()
    {
        return Recipe::with('ingredients')->get();
    }

    public function getRandomRecipes($quantity)
    {
        $allRecipeIds = Recipe::all();
        $recipes = [];
        for ($i = 0; $i < $quantity; $i++) {
            $randomRecipe = $allRecipeIds->random();
            if (!isset($recipes[$randomRecipe->id])) {
                $recipes[$randomRecipe->id] = $randomRecipe->toArray();
                $recipes[$randomRecipe->id]['quantity'] = 1;
                continue;
            }
            $recipes[$randomRecipe->id]['quantity'] += 1;
        }
        return array_values($recipes);
    }

    static function addOrder($data)
    {
        $orderData = json_decode($data, true);
        $itemsInsert = [];
        DB::beginTransaction();
        foreach ($orderData['items'] as $item) {
            $itemsInsert[] = [
                'order_id' => $orderData['id'],
                'recipe_id' => $item['recipe_id'],
                'quantity' => $item['quantity']
            ];
        }
        DB::table('order_items')->insert($itemsInsert);
        Order::where('id', $orderData['id'])->update(['status' => 'preparing']);
        DB::commit();
    }

    static function cookOrder($data)
    {
        $orderData = json_decode($data, true);
        DB::beginTransaction();
        foreach ($orderData['ingredients'] as $item) {
            Ingredient::where('id', $item['id'])
              ->update(['quantity' => DB::raw('quantity - ' . $item['quantity'])]);
        }
        Order::where('id', $orderData['id'])->update(['status' => 'delivered']);
        DB::commit();
    }
}