<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private $inventoryService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function getPurchases(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $ingredient_id = $request->input('ingredient_id', null);
            $purchases = $this->inventoryService->getPurchases($page, $ingredient_id);
            return response()->json(['data' => $purchases], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getIngredients()
    {
        try {
            $ingredients = $this->inventoryService->getIngredients();
            return response()->json(['data' => $ingredients], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
