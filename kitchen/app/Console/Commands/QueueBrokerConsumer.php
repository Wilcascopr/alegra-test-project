<?php

namespace App\Console\Commands;

use App\Services\QueueBrokerService;
use Illuminate\Console\Command;

class QueueBrokerConsumer extends Command
{
    private $qbService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queuebroker:consume {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from the given queue';

    public function __construct(QueueBrokerService $qbService)
    {
        parent::__construct();
        $this->qbService = $qbService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->qbService->consume($this->argument('queue'));
    }
}