<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KitchenService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller
{
    private $kitchenService;
    private $inventoryService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(KitchenService $kitchenService, InventoryService $inventoryService)
    {
        $this->kitchenService = $kitchenService;
        $this->inventoryService = $inventoryService;
    }

    public function getRandomRecipes(Request $request)
    {   
        $quantity = $request->query('quantity', 1);
        if ($quantity < 1 || $quantity > 20)
            return response()->json([
                'message' => 'The quantity must be between 1 and 20'
            ], 400);
        $query = ['quantity' => $quantity];
        try {
            $response = $this->kitchenService->getRandomRecipes($query);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get random recipes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecipes()
    {
        try {
            $response = $this->kitchenService->getRecipes();
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get recipes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrders()
    {
        try {
            $response = $this->kitchenService->getOrders();
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrder($id)
    {
        try {
            $response = $this->kitchenService->getOrder($id);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPurchases(Request $request)
    {
        $query = [
            'page' => $request->input('page', 1),
            'ingredient_id' => $request->input('ingredient_id', null)
        ];
        $validator = Validator::make($query, [
            'page' => 'integer|min:1',
            'ingredient_id' => 'nullable|integer|exists:ingredients,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }
        try {
            $response = $this->inventoryService->getPurchases($query);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get purchases',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getIngredients()
    {
        try {
            $response = $this->inventoryService->getIngredients();
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get ingredients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createOrder(Request $request)
    {
        $rules = [
            'order.items' => ['required', 'array', 'min:1', 'max:6', function ($attribute, $value, $fail) {
                $totalQuantity = 0;
                foreach ($value as $item) {
                    $totalQuantity += $item['quantity'] ?? 0;
                }
                if ($totalQuantity > 20) {
                    $fail('The maximum amount of dishes per order is 20.');
                }
            }],
            'order.items.*.recipe_id' => 'required|integer|exists:recipes,id',
            'order.items.*.quantity' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $response = $this->kitchenService->createOrder($validator->validated());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
