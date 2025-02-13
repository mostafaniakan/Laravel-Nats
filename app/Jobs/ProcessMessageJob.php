<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMessageJob implements ShouldQueue
{
    use Queueable;


    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }


    public function handle(): void
    {
        Log::info("Message received: {$this->message}");
    }
}
