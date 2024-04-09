<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Purchase;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use App\Jobs\PurchaseIngredientJob;
use Illuminate\Support\Facades\Bus;

class InventoryService
{

    private static $maxAttemptsCheckIngredients = 4;
    private static $queueBrokerService = null;

    public function getPurchases($page, $ingredient_id = null)
    {
        $limit = 20;
        return Purchase::with('ingredient')
            ->when($ingredient_id, function ($query, $ingredient_id) {
                return $query->where('ingredient_id', $ingredient_id);
            })->paginate($limit, ['*'], 'page', $page);
    }

    public function getIngredients()
    {
        return Ingredient::all();
    }

    static function checkOrderIngredients($orderData, $attemptsCheckIngredients = 0)
    {
        try {
            $attemptsCheckIngredients++;
            $order = json_decode($orderData, true);
            $itemsForCheck = [];
            $recipesIds = array_column($order['items'], 'recipe_id');
            DB::beginTransaction();
            $recipes = Recipe::with('ingredients')->whereIn('id', $recipesIds)->get();
            $itemsForCheck = array_column($order['items'], 'quantity', 'recipe_id');
            $needsPurchase = false;
            $ingredientsPurchase = [];
            foreach ($recipes as $recipe) {
                self::checkRecipe($recipe->ingredients, $itemsForCheck[$recipe->id], $ingredientsPurchase);
            }
            DB::commit();
            $cookData = [
                'id' => $order['id'],
                'ingredients' => []
            ];
            $jobsBatch = [];
            foreach ($ingredientsPurchase as $name => $item) {
                $cookData['ingredients'][] = [
                    'id' => $item['id'],
                    'quantity' => $item['quantity_needed']
                ];
                if (!$needsPurchase) $needsPurchase = $item['quantity_needed'] > $item['current_quantity'];
                if ($item['quantity_needed'] > $item['current_quantity']) $jobsBatch[] = new PurchaseIngredientJob([
                        'id' => $item['id'],
                        'name' => $name,
                        'quantity' => abs($item['quantity_needed'] - $item['current_quantity'])
                    ]);
            }
            if (is_null(self::$queueBrokerService)) self::$queueBrokerService = new QueueBrokerService(new RabbitMQService());
            if (!$needsPurchase) {
                self::$queueBrokerService->publish(json_encode($cookData), RabbitMQService::INGREDIENTS_EXCHANGE_READY);
                return;
            }
            Bus::batch($jobsBatch)
                ->catch(function () use ($orderData) {
                    if (is_null(InventoryService::$queueBrokerService)) InventoryService::$queueBrokerService = new QueueBrokerService(new RabbitMQService());
                    InventoryService::$queueBrokerService->publish($orderData, RabbitMQService::INGREDIENTS_EXCHANGE_RETRY);
                })
                ->finally(function () use ($attemptsCheckIngredients, $orderData) {
                    if ($attemptsCheckIngredients < InventoryService::$maxAttemptsCheckIngredients) {
                        InventoryService::checkOrderIngredients($orderData, $attemptsCheckIngredients);
                    }
                })
                ->dispatchIf($needsPurchase);
        } catch (\Exception $e) {
            DB::rollBack();
            if (is_null(self::$queueBrokerService)) self::$queueBrokerService = new QueueBrokerService(new RabbitMQService());
            self::$queueBrokerService->publish($orderData, RabbitMQService::INGREDIENTS_EXCHANGE_RETRY);
            throw $e;
        }
    }

    private static function checkRecipe($recipeIngredients, $amount, &$ingredientsPurchase)
    {
      foreach ($recipeIngredients as $ingredient) {
          $amountInner = $amount * $ingredient->pivot->quantity;
          if (!isset($ingredientsPurchase[$ingredient->name])) {
              $ingredientsPurchase[$ingredient->name] = [
                  'id' => $ingredient->id,
                  'current_quantity' => $ingredient->quantity,
                  'quantity_needed' => $amountInner,
              ];
              continue;
          }
          $ingredientsPurchase[$ingredient->name]['quantity_needed'] += $amountInner;
      }
    }
}
