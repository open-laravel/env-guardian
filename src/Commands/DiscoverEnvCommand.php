<?php

namespace OpenLaravel\EnvGuardian\Commands;

use Illuminate\Console\Command;
use OpenLaravel\EnvGuardian\Services\EnvComparator;
use OpenLaravel\EnvGuardian\Services\EnvParser;
use OpenLaravel\EnvGuardian\Services\EnvScanner;
use OpenLaravel\EnvGuardian\Services\EnvWriter;

class DiscoverEnvCommand extends Command
{
    public $signature = 'env:discover
                        {--write : Write discovered keys to .env.example}
                        {--mask-value= : Value to use as mask (default: empty)}';

    public $description = 'Discover env() usage in code and compare with .env.example';

    protected EnvScanner $scanner;

    protected EnvParser $parser;

    protected EnvComparator $comparator;

    protected EnvWriter $writer;

    public function __construct()
    {
        parent::__construct();

        $this->scanner = new EnvScanner;
        $this->parser = new EnvParser;
        $this->comparator = new EnvComparator;
        $this->writer = new EnvWriter;
    }

    public function handle(): int
    {
        $basePath = base_path();
        $examplePath = $basePath.'/.env.example';

        $this->info('🔍 Scanning for env() usage...');
        $this->newLine();

        // Scan for env() usage
        $discoveredKeys = $this->scanner->scan($basePath);

        if (empty($discoveredKeys)) {
            $this->warn('⚠ No env() usage found');

            return self::SUCCESS;
        }

        // Sort for better readability
        sort($discoveredKeys);

        // Get existing keys from .env.example
        $exampleKeys = file_exists($examplePath) ? $this->parser->getKeys($examplePath) : [];

        // Find missing keys
        $missingKeys = $this->comparator->getMissingKeys($discoveredKeys, $exampleKeys);

        // Display results
        $this->info('📊 Discovery Results:');
        $this->line("   Total keys discovered: ".count($discoveredKeys));
        $this->line("   Keys in .env.example: ".count($exampleKeys));
        $this->line("   Missing from .env.example: ".count($missingKeys));
        $this->newLine();

        if (! empty($missingKeys)) {
            $this->comment('Keys used in code but missing from .env.example:');
            foreach ($missingKeys as $key) {
                $this->line("   • {$key}");
            }
            $this->newLine();
        } else {
            $this->info('✔ All discovered keys are already in .env.example');

            return self::SUCCESS;
        }

        // Write if requested
        if ($this->option('write')) {
            $maskValue = $this->option('mask-value') ?? config('env-guardian.mask_with', '');
            $added = $this->writer->appendKeys($examplePath, $missingKeys, $maskValue);

            $this->info("✔ Added {$added} key(s) to .env.example");
        } else {
            $this->comment('💡 Run with --write to add these keys to .env.example');
        }

        return self::SUCCESS;
    }
}
