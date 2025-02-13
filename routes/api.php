<?php

use App\Http\Controllers\NatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/nats/ping', [NatsController::class, 'ping']);
Route::get('/nats/send', [NatsController::class, 'sendMessage']);
Route::get('/nats/receive', [NatsController::class, 'receiveMessage']);
