<?php

namespace App\Http\Controllers;

use App\Services\KitchenService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    private $kitchenService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(KitchenService $kitchenService)
    {
        $this->kitchenService = $kitchenService;
    }

    public function createEmptyOrder()
    {
        try {
            $orderId = $this->kitchenService->createEmptyOrder();
            return response()->json([ 'data' => ['id' => $orderId]], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        
        }
    }

    public function getOrders(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $orders = $this->kitchenService->getOrders($page);
            return response()->json(['data' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOrder($id)
    {   
        $validator = Validator::make(['id' => $id], ['id' => 'required|integer|exists:orders,id']);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $order = $this->kitchenService->getOrder($id);
            return response()->json(['data' => $order], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRecipes()
    {
        try {
            $recipes = $this->kitchenService->getRecipes();
            return response()->json(['data' => $recipes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRandomRecipes(Request $request)
    {
        $quantity = $request->query('quantity', 1);
        try {
            $randomRecipes = $this->kitchenService->getRandomRecipes($quantity);
            return response()->json(['data' => $randomRecipes], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to get random recipes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
