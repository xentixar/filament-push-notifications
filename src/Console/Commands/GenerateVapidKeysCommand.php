<?php

namespace Xentixar\FilamentPushNotifications\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:generate-vapid-keys {--update-env : Automatically update the .env file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VAPID keys for web push notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating VAPID keys...');
        $this->newLine();

        $keys = VAPID::createVapidKeys();

        $this->line('VAPID keys generated successfully!');
        $this->newLine();

        $this->line('Public Key:');
        $this->info($keys['publicKey']);
        $this->newLine();

        $this->line('Private Key:');
        $this->info($keys['privateKey']);
        $this->newLine();

        if ($this->option('update-env')) {
            $this->updateEnvFile($keys);
        } else {
            $this->warn('Add these keys to your .env file:');
            $this->newLine();
            $this->line("VAPID_PUBLIC_KEY=\"{$keys['publicKey']}\"");
            $this->line("VAPID_PRIVATE_KEY=\"{$keys['privateKey']}\"");
            $this->line('VAPID_SUBJECT="mailto:your-email@example.com"');
            $this->newLine();
            $this->comment('Tip: Use --update-env flag to automatically update your .env file');
        }

        return self::SUCCESS;
    }

    /**
     * Update the .env file with VAPID keys.
     */
    protected function updateEnvFile(array $keys): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = file_get_contents($envPath);

        // Check if keys already exist
        if (str_contains($envContent, 'VAPID_PUBLIC_KEY')) {
            if (!$this->confirm('VAPID keys already exist in .env. Do you want to replace them?', false)) {
                $this->info('Skipped updating .env file.');
                return;
            }

            // Replace existing keys
            $envContent = preg_replace(
                '/VAPID_PUBLIC_KEY=.*/',
                "VAPID_PUBLIC_KEY=\"{$keys['publicKey']}\"",
                $envContent
            );
            $envContent = preg_replace(
                '/VAPID_PRIVATE_KEY=.*/',
                "VAPID_PRIVATE_KEY=\"{$keys['privateKey']}\"",
                $envContent
            );
        } else {
            // Append new keys
            $envContent .= "\n# Web Push VAPID Keys\n";
            $envContent .= "VAPID_PUBLIC_KEY=\"{$keys['publicKey']}\"\n";
            $envContent .= "VAPID_PRIVATE_KEY=\"{$keys['privateKey']}\"\n";
            $envContent .= "VAPID_SUBJECT=\"mailto:admin@example.com\"\n";
        }

        file_put_contents($envPath, $envContent);

        $this->info('.env file updated successfully!');
        $this->newLine();
        $this->warn('Remember to update VAPID_SUBJECT with your actual contact email.');
    }
}
