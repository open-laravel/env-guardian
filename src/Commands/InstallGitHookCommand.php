<?php

namespace OpenLaravel\EnvGuardian\Commands;

use Illuminate\Console\Command;
use OpenLaravel\EnvGuardian\Services\GitHookInstaller;

class InstallGitHookCommand extends Command
{
    public $signature = 'env:install-hook
                        {--force : Overwrite existing hook}
                        {--uninstall : Remove the hook}';

    public $description = 'Install or uninstall the pre-commit Git hook';

    protected GitHookInstaller $installer;

    public function __construct()
    {
        parent::__construct();

        $this->installer = new GitHookInstaller;
    }

    public function handle(): int
    {
        $basePath = base_path();

        // Check if .git directory exists
        if (! is_dir($basePath.'/.git')) {
            $this->error('⚠ Not a Git repository');

            return self::FAILURE;
        }

        // Uninstall if requested
        if ($this->option('uninstall')) {
            return $this->uninstall($basePath);
        }

        // Install hook
        return $this->install($basePath);
    }

    protected function install(string $basePath): int
    {
        $force = $this->option('force');

        // Check if hook already exists
        if ($this->installer->isInstalled($basePath)) {
            if (! $force) {
                $this->info('✔ Pre-commit hook is already installed');

                return self::SUCCESS;
            }

            $this->warn('⚠ Overwriting existing hook...');
        } elseif (file_exists($basePath.'/.git/hooks/pre-commit') && ! $force) {
            $this->warn('⚠ A pre-commit hook already exists');
            $confirm = $this->confirm('Do you want to overwrite it?', false);

            if (! $confirm) {
                $this->comment('Installation cancelled');

                return self::SUCCESS;
            }

            $force = true;
        }

        // Install the hook
        $result = $this->installer->install($basePath, $force);

        if ($result['success']) {
            $this->info('✔ '.$result['message']);
            $this->newLine();
            $this->comment('💡 The hook will warn you about missing keys in .env.example');
            $this->comment('   but will NOT block commits');

            return self::SUCCESS;
        }

        $this->error('❌ '.$result['message']);

        return self::FAILURE;
    }

    protected function uninstall(string $basePath): int
    {
        $result = $this->installer->uninstall($basePath);

        if ($result['success']) {
            $this->info('✔ '.$result['message']);

            return self::SUCCESS;
        }

        $this->error('⚠ '.$result['message']);

        return self::FAILURE;
    }
}
