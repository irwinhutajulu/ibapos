<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ToggleDeveloperMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:toggle {--status : Show current status only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toggle developer mode (auto-login) for local development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = file_get_contents($envPath);
        $currentStatus = $this->getCurrentStatus($envContent);
        
        if ($this->option('status')) {
            $this->info("Developer Mode is currently: " . ($currentStatus ? 'ENABLED' : 'DISABLED'));
            return 0;
        }

        $newStatus = !$currentStatus;
        $newContent = $this->updateDeveloperMode($envContent, $newStatus);
        
        file_put_contents($envPath, $newContent);
        
        $statusText = $newStatus ? 'ENABLED' : 'DISABLED';
        $this->info("Developer Mode has been {$statusText}");
        
        if ($newStatus) {
            $this->warn('🔥 Auto-login is now active in local environment');
            $this->line('   • No authentication required');
            $this->line('   • Automatically login as first user');
            $this->line('   • DEV indicator will show in UI');
        } else {
            $this->info('🔒 Normal authentication is restored');
            $this->line('   • Login required to access protected routes');
            $this->line('   • Standard Laravel authentication flow');
        }
        
        $this->newLine();
        $this->comment('Run: php artisan config:clear to apply changes');
        
        return 0;
    }
    
    private function getCurrentStatus(string $envContent): bool
    {
        if (preg_match('/^DEVELOPER_MODE=(.*)$/m', $envContent, $matches)) {
            return strtolower(trim($matches[1])) === 'true';
        }
        return false;
    }
    
    private function updateDeveloperMode(string $envContent, bool $enable): string
    {
        $value = $enable ? 'true' : 'false';
        
        if (preg_match('/^DEVELOPER_MODE=.*$/m', $envContent)) {
            // Update existing line
            return preg_replace('/^DEVELOPER_MODE=.*$/m', "DEVELOPER_MODE={$value}", $envContent);
        } else {
            // Add new line after APP_URL
            return preg_replace(
                '/^(APP_URL=.*?)$/m',
                "$1\n\n# Developer Mode - Auto login without authentication in local environment\nDEVELOPER_MODE={$value}",
                $envContent
            );
        }
    }
}
