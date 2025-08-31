<?php

return [
    /**
     * The model to use for the receivers.
     */
    'receiver_model' => \App\Models\User::class,

    /**
     * The socket configuration.
     */
    'socket' => [
        'host' => env('SOCKEON_HOST', 'localhost'),
        'port' => env('SOCKEON_PORT', 8080),
        'key' => env('SOCKEON_KEY', 'secret'),
        'debug' => env('SOCKEON_DEBUG', true),
        'allowed_origins' => ['http://127.0.0.1:8000', 'http://localhost:8000'],
    ],
];