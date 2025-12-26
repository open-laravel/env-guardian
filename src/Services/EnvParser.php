<?php

namespace OpenLaravel\EnvGuardian\Services;

class EnvParser
{
    /**
     * Parse an environment file and return an array of key-value pairs
     *
     * @return array<string, string>
     */
    public function parse(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Remove 'export ' prefix if present
            if (str_starts_with($line, 'export ')) {
                $line = substr($line, 7);
            }

            // Parse KEY=VALUE
            $position = strpos($line, '=');
            if ($position === false) {
                continue;
            }

            $key = substr($line, 0, $position);
            $value = substr($line, $position + 1);

            // Clean the key
            $key = trim($key);

            // Handle quoted values
            $value = $this->parseValue($value);

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Get all keys from an environment file
     *
     * @return array<string>
     */
    public function getKeys(string $filePath): array
    {
        return array_keys($this->parse($filePath));
    }

    /**
     * Parse a value, handling quoted strings
     */
    protected function parseValue(string $value): string
    {
        $value = trim($value);

        // Handle single or double quoted values
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Get the raw content of a file
     */
    public function getContent(string $filePath): string
    {
        if (! file_exists($filePath)) {
            return '';
        }

        return file_get_contents($filePath);
    }
}
