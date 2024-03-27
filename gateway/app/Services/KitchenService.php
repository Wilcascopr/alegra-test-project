<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KitchenService
{
    private $qbService;
    private $baseUrl;

    public function __construct(QueueBrokerService $qbService)
    {
        $this->qbService = $qbService;
        $this->baseUrl = env('KITCHEN_SERVICE');
    }

    public function getRandomRecipes($query)
    {
        $url = $this->baseUrl . '/random-recipes?' . http_build_query($query);
        $res = Http::get($url);
        if ($res->status() !== 200) {
            throw new \Exception('An error occurred while trying to get the data');
        }
        return $res;
    }

    public function getRecipes()
    {
        $url = $this->baseUrl . '/recipes';
        $res = Http::get($url);
        if ($res->status() !== 200) {
            throw new \Exception('An error occurred while trying to get the data');
        }
        return $res;
    }

    public function getOrders()
    {
        $url = $this->baseUrl . '/orders';
        $res = Http::get($url);
        if ($res->status() !== 200) {
            throw new \Exception('An error occurred while trying to get the data');
        }
        return $res;
    }

    public function getOrder($id)
    {
        $url = $this->baseUrl . "/orders/{$id}";
        $res = Http::get($url);
        if ($res->status() !== 200) {
            throw new \Exception('An error occurred while trying to get the data');
        }
        return $res;
    }

    public function createOrder($input)
    {
        $url =$this->baseUrl . "/empty-order";
        $responseEmptyOrder = Http::post($url);
        if ($responseEmptyOrder->status() !== 201) {
            throw new \Exception('An error occurred while trying to get the data. ' . $responseEmptyOrder->json()['message']);
        }
        $order = $input['order'];
        $order['id'] = $responseEmptyOrder->json()['data']['id'];
        $data = json_encode($order);
        $this->qbService->publish($data, RabbitMQService::ORDER_EXCHANGE_REQUESTED);
        return [
            'message' => 'Order was requested successfully, the restaurant will be preparing it soon.',
            'order' => $order
        ];
    }
}