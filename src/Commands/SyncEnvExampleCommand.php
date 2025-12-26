<?php

namespace OpenLaravel\EnvGuardian\Commands;

use Illuminate\Console\Command;
use OpenLaravel\EnvGuardian\Services\EnvComparator;
use OpenLaravel\EnvGuardian\Services\EnvParser;
use OpenLaravel\EnvGuardian\Services\EnvWriter;

class SyncEnvExampleCommand extends Command
{
    public $signature = 'env:sync-example
                        {--dry-run : Display changes without writing to file}
                        {--sort : Sort keys alphabetically}
                        {--grouped : Group keys with comment headers}
                        {--mask-value= : Value to use as mask (default: empty)}';

    public $description = 'Sync missing keys from .env to .env.example';

    protected EnvParser $parser;

    protected EnvComparator $comparator;

    protected EnvWriter $writer;

    public function __construct()
    {
        parent::__construct();

        $this->parser = new EnvParser;
        $this->comparator = new EnvComparator;
        $this->writer = new EnvWriter;
    }

    public function handle(): int
    {
        $basePath = base_path();
        $envPath = $basePath.'/.env';
        $examplePath = $basePath.'/.env.example';

        if (! file_exists($envPath)) {
            $this->error('⚠ .env file not found');

            return self::FAILURE;
        }

        // Parse both files
        $envKeys = $this->parser->getKeys($envPath);
        $exampleKeys = $this->parser->getKeys($examplePath);

        // Find missing keys
        $missingKeys = $this->comparator->getMissingKeys($envKeys, $exampleKeys);

        // Display summary
        $this->info('📊 Environment Summary:');
        $this->line("   Total keys in .env: {$this->colorize(count($envKeys), 'cyan')}");
        $this->line("   Existing keys in .env.example: {$this->colorize(count($exampleKeys), 'cyan')}");
        $this->line("   Missing keys: {$this->colorize(count($missingKeys), count($missingKeys) > 0 ? 'yellow' : 'green')}");

        if (empty($missingKeys)) {
            $this->newLine();
            $this->info('✔ All keys are already in sync!');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->comment('Missing keys to be added:');
        foreach ($missingKeys as $key) {
            $this->line("   • {$key}");
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('ℹ Dry run mode - no changes made');

            return self::SUCCESS;
        }

        // Get options
        $maskValue = $this->option('mask-value') ?? config('env-guardian.mask_with', '');
        $sort = $this->option('sort');
        $grouped = $this->option('grouped') ?? config('env-guardian.grouped', false);

        // Write changes
        $added = $this->writer->appendKeys($examplePath, $missingKeys, $maskValue, $sort, $grouped);

        $this->newLine();
        $this->info("✔ Successfully added {$added} key(s) to .env.example");

        return self::SUCCESS;
    }

    protected function colorize(int $value, string $color): string
    {
        $colors = [
            'cyan' => "\e[36m",
            'yellow' => "\e[33m",
            'green' => "\e[32m",
            'reset' => "\e[0m",
        ];

        return ($colors[$color] ?? '').$value.($colors['reset'] ?? '');
    }
}
