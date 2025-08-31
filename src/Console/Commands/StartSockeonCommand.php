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
    protected $signature = 'start:sockeon {--host=0.0.0.0} {--port=2025} {--debug=true}';

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
        $config->host = $this->option('host');
        $config->port = $this->option('port');
        $config->debug = $this->option('debug');
        $config->cors = [
            'allowed_origins' => ['http://127.0.0.1:8000', 'http://localhost:8000'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization'],
            'allowed_credentials' => true,
            'max_age' => 3600,
        ];

        $server = new Server($config);
        $server->registerControllers([SockeonAuthController::class]);
        $server->run();
    }
}
