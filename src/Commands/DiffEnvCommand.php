<?php

namespace OpenLaravel\EnvGuardian\Commands;

use Illuminate\Console\Command;
use OpenLaravel\EnvGuardian\Services\EnvComparator;
use OpenLaravel\EnvGuardian\Services\EnvParser;

class DiffEnvCommand extends Command
{
    public $signature = 'env:diff
                        {--quiet : Minimal output for CI/scripting}';

    public $description = 'Compare .env and .env.example files';

    protected EnvParser $parser;

    protected EnvComparator $comparator;

    public function __construct()
    {
        parent::__construct();

        $this->parser = new EnvParser;
        $this->comparator = new EnvComparator;
    }

    public function handle(): int
    {
        $basePath = base_path();
        $envPath = $basePath.'/.env';
        $examplePath = $basePath.'/.env.example';

        if (! file_exists($envPath)) {
            if (! $this->option('quiet')) {
                $this->error('⚠ .env file not found');
            }

            return self::FAILURE;
        }

        if (! file_exists($examplePath)) {
            if (! $this->option('quiet')) {
                $this->error('⚠ .env.example file not found');
            }

            return self::FAILURE;
        }

        // Parse both files
        $envKeys = $this->parser->getKeys($envPath);
        $exampleKeys = $this->parser->getKeys($examplePath);

        // Compare
        $diff = $this->comparator->compare($envKeys, $exampleKeys);

        if ($this->option('quiet')) {
            if (! empty($diff['missing_in_example'])) {
                $this->line(count($diff['missing_in_example']).' keys missing in .env.example');
            }

            if (! empty($diff['missing_in_env'])) {
                $this->line(count($diff['missing_in_env']).' keys missing in .env');
            }

            return self::SUCCESS;
        }

        // Display results
        $this->info('📊 Environment Diff:');
        $this->newLine();

        if (empty($diff['missing_in_example']) && empty($diff['missing_in_env'])) {
            $this->info('✔ Files are in sync!');

            return self::SUCCESS;
        }

        // Missing in .env.example
        if (! empty($diff['missing_in_example'])) {
            $this->warn('⚠ Keys in .env but missing in .env.example:');
            foreach ($diff['missing_in_example'] as $key) {
                $this->line("   • {$key}");
            }
            $this->newLine();
            $this->comment('💡 Run: php artisan env:sync-example');
            $this->newLine();
        }

        // Missing in .env
        if (! empty($diff['missing_in_env'])) {
            $this->comment('ℹ Keys in .env.example but missing in .env:');
            foreach ($diff['missing_in_env'] as $key) {
                $this->line("   • {$key}");
            }
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
