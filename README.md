# Laravel-Nats
This package provides an observer for the Laravel Nats push-based event system that uses Laravel events. it uses https://github.com/basis-company/nats.php for using Nats in php

## Installation
You can install the package via Composer:

```bash
composer require basis-company/nats
```
The package will automatically register itself.

## Connection
Here’s an explanation of each variable in your `.env` file for the NATS configuration:

```
NATS_HOST=nats://localhost:4222
NATS_USER=basis
NATS_PASS=secret
NATS_TLS_CERT_FILE=/path/to/cert.pem
NATS_TLS_KEY_FILE=/path/to/key.pem
NATS_TLS_CA_FILE=/path/to/ca-cert.pem
```

other configuration parameters can be found in `config/nats.php`.

```
return [
    'host' => env('NATS_HOST', 'localhost'),
    'user' => env('NATS_USER', 'user'),
    'pass' => env('NATS_PASS', 'password'),
    'tls_cert_file' => env('NATS_TLS_CERT_FILE', null),
    'tls_key_file' => env('NATS_TLS_KEY_FILE', null),
    'tls_ca_file' => env('NATS_TLS_CA_FILE', null),
];
```
### Service Registration in `AppServiceProvider.php`:

```php
  public function register(): void
    {
        $this->app->singleton(NatsService::class, function ($app) {
            return new NatsService();
        });
    }
```


## Usage 
 
### 1:In the `jobs/ProcessMessageJob.php`:

The `ProcessMessageJob` class is a Laravel job that implements the `ShouldQueue` interface, which means it is queued for background processing. When a message is received from NATS (as shown in the `receiveMessage` method), the job is dispatched to handle the message’s payload asynchronously.
```php
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
```


### 2: In the `Controllers/NatsController.php`:

this code is using dependency injection to inject the `NatsService` into the `controller`.
The NatsService class is responsible for handling `NATS` messaging tasks like publishing and subscribing to messages. 

```php
use App\Services\NatsService;

protected $natsService;

    public function __construct(NatsService $natsService)
    {
        $this->natsService = $natsService;
    }
```


The `ping` method in the `NatsController` is used to check if the application is successfully connected to the NATS server. It leverages the `NatsService` that was injected earlier to perform the actual check.

```php
public function ping()
    {
        $status = $this->natsService->ping();
        return response()->json(['status' => $status ? 'connected' : 'not connected']);
    }
```


The `sendMessage` method in the `NatsController` is responsible for publishing a message to the NATS server. It uses the NatsService to send a message to a specific NATS subject.

```php
public function sendMessage()
    {
        $this->natsService->publish('test.subject', 'Hello from Laravel!');
        return response()->json(['status' => 'message sent']);
    }
```


The `receiveMessage` method is responsible for subscribing to a NATS subject and processing any incoming messages on that subject. When a message is received, it will trigger the execution of a job (e.g., `ProcessMessageJob`) to handle the message’s payload.
```php
 public function receiveMessage()
    {
        $this->natsService->subscribe('test.subject', function ($message) {
            ProcessMessageJob::dispatch($message->payload);
        });

        $this->natsService->process();
    }
```


