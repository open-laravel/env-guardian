<?php

// config for OpenLaravel/EnvGuardian
return [
    /*
     * List of required environment keys that must be present in .env
     * These will be checked by the env:check command
     */
    'required_keys' => [
        // 'APP_KEY',
        // 'APP_URL',
        // 'DB_CONNECTION',
    ],

    /*
     * Whether to mask values when syncing to .env.example
     * If true, values will be replaced with the mask_with string
     */
    'mask_values' => true,

    /*
     * The string to use as a mask for values in .env.example
     * Only used when mask_values is true
     */
    'mask_with' => '',

    /*
     * Whether to group keys with comment headers when syncing
     * Groups will be based on key prefixes (e.g., APP_, DB_, etc.)
     */
    'grouped' => false,

    /*
     * Whether to fail with non-zero exit code in CI mode
     * Used by env:check --ci flag
     */
    'ci_fail_on_error' => true,
];
