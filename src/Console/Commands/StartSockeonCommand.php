<?php

namespace Xentixar\FilamentPushNotifications\Console\Commands;

use Illuminate\Console\Command;
use Sockeon\Sockeon\Config\ServerConfig;
use Sockeon\Sockeon\Connection\Server;
use Xentixar\FilamentPushNotifications\Controllers\SockeonAuthController;

class StartSockeonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:sockeon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the sockeon server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = new ServerConfig();
        $config->host = config('filament-push-notifications.socket.host');
        $config->port = config('filament-push-notifications.socket.port');
        $config->debug = config('filament-push-notifications.socket.debug');
        $config->cors = [
            'allowed_origins' => config('filament-push-notifications.socket.allowed_origins'),
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization'],
            'allowed_credentials' => true,
            'max_age' => 3600,
        ];
        $config->authKey = config('filament-push-notifications.socket.key');

        $server = new Server($config);
        $server->registerControllers([SockeonAuthController::class]);
        $server->run();
    }
}
