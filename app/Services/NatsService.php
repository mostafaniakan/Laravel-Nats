<?php

namespace App\Services;

use Basis\Nats\Client;
use Basis\Nats\Configuration;
use Exception;
use Illuminate\Support\Facades\Log;

class NatsService
{
    protected $client;

    public function __construct()
    {
        // پیکربندی اتصال به NATS
        $configuration = new Configuration(
            host: config('nats.host'),
            user: config('nats.user'),
            pass: config('nats.pass')
        );

        // اتصال با TLS در صورت نیاز
        if (config('nats.tls_cert_file')) {
            $configuration->setTlsCertFile(config('nats.tls_cert_file'))
                ->setTlsKeyFile(config('nats.tls_key_file'))
                ->setTlsCaFile(config('nats.tls_ca_file'));
        }

        // ایجاد کلاینت NATS
        $this->client = new Client($configuration);
    }

    public function ping()
    {
        try {
            return $this->client->ping();
        } catch (Exception $e) {
            Log::error('NATS Ping failed: ' . $e->getMessage());
            return false;
        }
    }

    public function publish($subject, $message)
    {
        $this->client->publish($subject, $message);
    }

    public function subscribe($subject, callable $callback)
    {
        $queue = $this->client->subscribe($subject);
        $queue->setTimeout(1);

        // پردازش پیام‌ها با استفاده از کال‌بک
        while ($message = $queue->next()) {
            $callback($message);
        }
    }

    public function process()
    {
        $this->client->process();
    }
}
