<?php

namespace OpenLaravel\EnvGuardian\Services;

class GitHookInstaller
{
    /**
     * Install the pre-commit hook
     *
     * @param  string  $basePath
     * @param  bool  $force
     * @return array{success: bool, message: string}
     */
    public function install(string $basePath, bool $force = false): array
    {
        $hookPath = $basePath.'/.git/hooks/pre-commit';

        if (file_exists($hookPath) && ! $force) {
            return [
                'success' => false,
                'message' => 'A pre-commit hook already exists. Use --force to overwrite it.',
            ];
        }

        $hookContent = $this->getHookContent();

        file_put_contents($hookPath, $hookContent);
        chmod($hookPath, 0755);

        return [
            'success' => true,
            'message' => 'Pre-commit hook installed successfully.',
        ];
    }

    /**
     * Get the content of the pre-commit hook
     *
     * @return string
     */
    protected function getHookContent(): string
    {
        return <<<'BASH'
#!/bin/bash

# Env Guardian Pre-commit Hook
# This hook checks for missing keys in .env.example (NON-BLOCKING)

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env and .env.example exist
if [ ! -f .env ] || [ ! -f .env.example ]; then
    exit 0
fi

# Run the env:diff command to check for missing keys
php artisan env:diff --quiet 2>&1 | grep -q "missing in .env.example"

if [ $? -eq 0 ]; then
    echo -e "${YELLOW}⚠ Warning: Some environment keys in .env are missing from .env.example${NC}"
    echo -e "${YELLOW}Run 'php artisan env:sync-example' to sync them.${NC}"
    echo ""
fi

# Always exit 0 (non-blocking)
exit 0

BASH;
    }

    /**
     * Uninstall the pre-commit hook
     *
     * @param  string  $basePath
     * @return array{success: bool, message: string}
     */
    public function uninstall(string $basePath): array
    {
        $hookPath = $basePath.'/.git/hooks/pre-commit';

        if (! file_exists($hookPath)) {
            return [
                'success' => false,
                'message' => 'No pre-commit hook found.',
            ];
        }

        // Only remove if it's our hook
        $content = file_get_contents($hookPath);
        if (! str_contains($content, 'Env Guardian Pre-commit Hook')) {
            return [
                'success' => false,
                'message' => 'The pre-commit hook does not appear to be from Env Guardian.',
            ];
        }

        unlink($hookPath);

        return [
            'success' => true,
            'message' => 'Pre-commit hook uninstalled successfully.',
        ];
    }

    /**
     * Check if the hook is installed
     *
     * @param  string  $basePath
     * @return bool
     */
    public function isInstalled(string $basePath): bool
    {
        $hookPath = $basePath.'/.git/hooks/pre-commit';

        if (! file_exists($hookPath)) {
            return false;
        }

        $content = file_get_contents($hookPath);

        return str_contains($content, 'Env Guardian Pre-commit Hook');
    }
}
