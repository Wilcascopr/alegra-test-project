<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class InventoryService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('INVENTORY_SERVICE');
    }

    public function getPurchases($query)
    {
        $url = $this->baseUrl . '/purchases?' . http_build_query($query);
        return Http::get($url);
    }

    public function getIngredients()
    {
        $url = $this->baseUrl . '/ingredients';
        return Http::get($url);
    } 
}