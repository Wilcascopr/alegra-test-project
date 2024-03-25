<?php

namespace App\Services;

class QueueBrokerService 
{

    private $queueServiceProvider;

    public function __construct(RabbitMQService $queueServiceProvider)
    {
        $this->queueServiceProvider = $queueServiceProvider;
    }

    public function publish($message, $exchange)
    {
        echo " [x] Sent $message to $exchange[queue] / $exchange[routing_key] " . PHP_EOL; 
        $this->queueServiceProvider->publish($message, $exchange);
    }

    public function consume($queue = null)
    {
        echo 'Waiting for new message on ' . $queue . PHP_EOL;
        $this->queueServiceProvider->consume($queue);
    }
}