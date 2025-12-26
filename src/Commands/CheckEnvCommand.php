<?php

namespace OpenLaravel\EnvGuardian\Commands;

use Illuminate\Console\Command;
use OpenLaravel\EnvGuardian\Services\EnvParser;

class CheckEnvCommand extends Command
{
    public $signature = 'env:check
                        {--ci : CI mode - exit with non-zero code if required keys are missing}';

    public $description = 'Check for required environment keys';

    protected EnvParser $parser;

    public function __construct()
    {
        parent::__construct();

        $this->parser = new EnvParser;
    }

    public function handle(): int
    {
        $basePath = base_path();
        $envPath = $basePath.'/.env';

        if (! file_exists($envPath)) {
            $this->error('⚠ .env file not found');

            return $this->option('ci') ? self::FAILURE : self::SUCCESS;
        }

        $requiredKeys = config('env-guardian.required_keys', []);

        if (empty($requiredKeys)) {
            $this->info('ℹ No required keys configured');
            $this->comment('💡 Add required keys in config/env-guardian.php');

            return self::SUCCESS;
        }

        // Get existing keys
        $envKeys = $this->parser->getKeys($envPath);

        // Find missing keys
        $missingKeys = array_diff($requiredKeys, $envKeys);

        $this->info('📊 Environment Check:');
        $this->line('   Required keys: '.count($requiredKeys));
        $this->line('   Missing keys: '.count($missingKeys));
        $this->newLine();

        if (empty($missingKeys)) {
            $this->info('✔ All required keys are present!');

            return self::SUCCESS;
        }

        // Display missing keys
        $this->warn('⚠ Missing required keys:');
        foreach ($missingKeys as $key) {
            $this->line("   • {$key}");
        }
        $this->newLine();

        // Check if we should fail in CI mode
        $shouldFail = $this->option('ci') && config('env-guardian.ci_fail_on_error', true);

        if ($shouldFail) {
            $this->error('❌ CI check failed due to missing required keys');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
