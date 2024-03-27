<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class QueueBrokerService 
{

    private $queueServiceProvider;

    public function __construct(RabbitMQService $queueServiceProvider)
    {
        $this->queueServiceProvider = $queueServiceProvider;
    }

    public function publish($message, $exchange)
    {
        echo "Publishing message to exchange:", json_encode($exchange), $message, " \n";
        $this->queueServiceProvider->publish($message, $exchange);
    }

    public function consume($queue = null)
    {
        echo "Consuming messages from queue: $queue\n";
        $this->queueServiceProvider->consume($queue);
    }
}