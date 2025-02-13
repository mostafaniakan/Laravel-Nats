<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMessageJob;
use App\Services\NatsService;
use Illuminate\Http\Request;

class NatsController extends Controller
{
    protected $natsService;

    public function __construct(NatsService $natsService)
    {
        $this->natsService = $natsService;
    }

    public function ping()
    {
        $status = $this->natsService->ping();
        return response()->json(['status' => $status ? 'connected' : 'not connected']);
    }

    public function sendMessage()
    {
        $this->natsService->publish('test.subject', 'Hello from Laravel!');
        return response()->json(['status' => 'message sent']);
    }

    public function receiveMessage()
    {
        $this->natsService->subscribe('test.subject', function ($message) {
            ProcessMessageJob::dispatch($message->payload);
        });

        $this->natsService->process();
    }
}
