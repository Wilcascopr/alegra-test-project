<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class ExampleJob extends Job
{
    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->payload = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('ExampleJob is running', [$this->payload]);
    }
}
