<?php

namespace App\Services;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    private $credentials;
    private $queue;
    public $callbackConsumer;

    const ORDER_EXCHANGE_REQUESTED = [
        'queue' => 'order_queue_kitchen',
        'exchange' => 'order_exchange',
        'routing_key' => 'order.requested',
    ];

    public function __construct()
    {
        $this->credentials = $this->getCredentials();
        $this->setCallbackConsumer();
    }

    public function publish($message, $exchange)
    {
        $connection = new AMQPStreamConnection(
            $this->credentials['host'],
            $this->credentials['port'],
            $this->credentials['user'],
            $this->credentials['pass'],
            $this->credentials['vhost']
        );
        $channel = $connection->channel();
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, $exchange['exchange'], $exchange['routing_key']);
        $channel->close();
        $connection->close();
    }

    public function consume($queue = null)
    {
        $connection = new AMQPStreamConnection(
            $this->credentials['host'],
            $this->credentials['port'],
            $this->credentials['user'],
            $this->credentials['pass'],
            $this->credentials['vhost']
        );
        $this->queue = $queue ?? $this->credentials['queue'];
        $channel = $connection->channel();
        $channel->basic_consume($this->queue, '', false, true, false, false, $this->callbackConsumer);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    public function setCallbackConsumer()
    {
        $this->callbackConsumer = function ($message) {
            echo " [". $this->queue ."] Received ", $message->getBody(), "\n";
            // Log::info("message received from queue", ["message" => $message->getBody()]);
        };
    }

    private function getCredentials()
    {
        return [
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'pass' => env('RABBITMQ_PASS',  'guest'),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'queue' => env('RABBITMQ_QUEUE', 'default'),
        ];
    }
}