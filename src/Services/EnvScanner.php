<?php

namespace OpenLaravel\EnvGuardian\Services;

class EnvScanner
{
    /**
     * Scan config files and code for env() usage
     *
     * @return array<string>
     */
    public function scan(string $basePath): array
    {
        $keys = [];

        // Scan config directory
        $configPath = $basePath.'/config';
        if (is_dir($configPath)) {
            $keys = array_merge($keys, $this->scanDirectory($configPath));
        }

        // Scan app directory for env() usage
        $appPath = $basePath.'/app';
        if (is_dir($appPath)) {
            $keys = array_merge($keys, $this->scanDirectory($appPath));
        }

        // Scan routes directory for env() usage
        $routesPath = $basePath.'/routes';
        if (is_dir($routesPath)) {
            $keys = array_merge($keys, $this->scanDirectory($routesPath));
        }

        return array_unique($keys);
    }

    /**
     * Scan a directory for env() usage
     *
     * @return array<string>
     */
    protected function scanDirectory(string $directory): array
    {
        $keys = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $keys = array_merge($keys, $this->scanFile($file->getPathname()));
            }
        }

        return $keys;
    }

    /**
     * Scan a file for env() usage
     *
     * @return array<string>
     */
    protected function scanFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $keys = [];

        // Match env('KEY') and env("KEY") - support both uppercase and lowercase
        preg_match_all("/env\(['\"]([A-Za-z0-9_]+)['\"]/", $content, $matches);

        if (! empty($matches[1])) {
            $keys = array_merge($keys, $matches[1]);
        }

        return $keys;
    }
}
