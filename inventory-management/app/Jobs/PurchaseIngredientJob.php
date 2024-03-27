<?php

namespace App\Jobs;

use App\Models\Ingredient;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PurchaseIngredientJob extends Job
{

    private $totalPurchased;
    private $data;
    private $storeUrl = 'https://recruitment.alegra.com/api/farmers-market/buy';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->longPollingTotalPurchased();
    }

    private function longPollingTotalPurchased()
    {
        $this->totalPurchased = 0;
        $totalWait = 0;
        $waitTime = 700 * 1000;
        $maxWaitTime = 20 * 1000 * 1000;
        $purchaseInsert = [];
        while ($this->totalPurchased < $this->data['quantity']) {
            if ($totalWait >= $maxWaitTime) break;
            $purchased = $this->purchaseIngredient($this->data['name'], $this->data['id']);
            $date = date('Y-m-d H:i:s');
            $purchaseInsert[] = [
                'ingredient_id' => $this->data['id'],
                'quantity' => $purchased,
                'created_at' => $date,
                'updated_at' => $date,
            ];
            $this->totalPurchased += $purchased;
            usleep($waitTime);
            $totalWait += $waitTime;
        }
        try {
            DB::beginTransaction();
            Purchase::insert($purchaseInsert);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        if ($this->totalPurchased < $this->data['quantity']) {
            dispatch(new PurchaseIngredientJob([
                'id' => $this->data['id'],
                'name' => $this->data['name'],
                'quantity' => $this->data['quantity'] - $this->totalPurchased,
            ]));
        }
        $this->updateIngredientQuantity();
    }

    private function purchaseIngredient($name): int
    {
        $resp = Http::get(
            $this->storeUrl . '?' . http_build_query([
                'ingredient' => strtolower($name),
            ])
        );
        if ($resp->status() !== 200) {
            throw new \Exception('Failed to purchase');
        }
        $data = $resp->json();
        return $data['quantitySold'];
    }

    private function updateIngredientQuantity()
    {
        try {
            DB::beginTransaction();
            Ingredient::where('id', $this->data['id'])
                ->update(['quantity' => DB::raw('quantity + ' . $this->totalPurchased)]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
