<?php

namespace OpenLaravel\EnvGuardian\Services;

class EnvWriter
{
    /**
     * Append new keys to an environment file
     *
     * @param  string  $filePath
     * @param  array<string>  $keys
     * @param  string  $maskValue
     * @param  bool  $sort
     * @param  bool  $grouped
     * @return int Number of keys added
     */
    public function appendKeys(
        string $filePath,
        array $keys,
        string $maskValue = '',
        bool $sort = false,
        bool $grouped = false
    ): int {
        if (empty($keys)) {
            return 0;
        }

        $content = file_exists($filePath) ? file_get_contents($filePath) : '';

        // Ensure file ends with newline
        if (! empty($content) && ! str_ends_with($content, "\n")) {
            $content .= "\n";
        }

        if ($sort) {
            sort($keys);
        }

        if ($grouped) {
            $content .= $this->appendGroupedKeys($keys, $maskValue);
        } else {
            $content .= $this->appendPlainKeys($keys, $maskValue);
        }

        file_put_contents($filePath, $content);

        return count($keys);
    }

    /**
     * Append keys with grouping by prefix
     *
     * @param  array<string>  $keys
     * @param  string  $maskValue
     * @return string
     */
    protected function appendGroupedKeys(array $keys, string $maskValue): string
    {
        $grouped = [];

        foreach ($keys as $key) {
            $prefix = $this->getKeyPrefix($key);
            $grouped[$prefix][] = $key;
        }

        $content = '';
        foreach ($grouped as $prefix => $groupKeys) {
            if ($prefix !== 'OTHER') {
                $content .= "\n# {$prefix}\n";
            } else {
                $content .= "\n# Other\n";
            }

            foreach ($groupKeys as $key) {
                $content .= "{$key}={$maskValue}\n";
            }
        }

        return $content;
    }

    /**
     * Append keys without grouping
     *
     * @param  array<string>  $keys
     * @param  string  $maskValue
     * @return string
     */
    protected function appendPlainKeys(array $keys, string $maskValue): string
    {
        $content = '';

        if (! empty($keys)) {
            $content .= "\n";
        }

        foreach ($keys as $key) {
            $content .= "{$key}={$maskValue}\n";
        }

        return $content;
    }

    /**
     * Get the prefix of a key (e.g., APP from APP_NAME)
     *
     * @param  string  $key
     * @return string
     */
    protected function getKeyPrefix(string $key): string
    {
        $position = strpos($key, '_');

        if ($position === false) {
            return 'OTHER';
        }

        return substr($key, 0, $position);
    }

    /**
     * Sort the content of an environment file
     *
     * @param  string  $filePath
     * @return void
     */
    public function sortFile(string $filePath): void
    {
        if (! file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);

        $envLines = [];
        $otherLines = [];

        foreach ($lines as $line) {
            if (empty(trim($line)) || str_starts_with(trim($line), '#')) {
                $otherLines[] = $line;
            } elseif (str_contains($line, '=')) {
                $envLines[] = $line;
            } else {
                $otherLines[] = $line;
            }
        }

        sort($envLines);

        $newContent = implode("\n", array_merge($otherLines, [''], $envLines));
        file_put_contents($filePath, $newContent);
    }
}
