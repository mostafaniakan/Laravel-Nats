<?php

return [
    'host' => env('NATS_HOST', 'localhost'),
    'user' => env('NATS_USER', 'user'),
    'pass' => env('NATS_PASS', 'password'),
    'tls_cert_file' => env('NATS_TLS_CERT_FILE', null),
    'tls_key_file' => env('NATS_TLS_KEY_FILE', null),
    'tls_ca_file' => env('NATS_TLS_CA_FILE', null),
];


