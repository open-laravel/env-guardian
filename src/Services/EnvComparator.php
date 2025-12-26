<?php

namespace OpenLaravel\EnvGuardian\Services;

class EnvComparator
{
    /**
     * Compare two sets of keys and return the differences
     *
     * @param  array<string>  $envKeys
     * @param  array<string>  $exampleKeys
     * @return array{missing_in_example: array<string>, missing_in_env: array<string>}
     */
    public function compare(array $envKeys, array $exampleKeys): array
    {
        return [
            'missing_in_example' => array_values(array_diff($envKeys, $exampleKeys)),
            'missing_in_env' => array_values(array_diff($exampleKeys, $envKeys)),
        ];
    }

    /**
     * Get keys that exist in the first array but not in the second
     *
     * @param  array<string>  $keys1
     * @param  array<string>  $keys2
     * @return array<string>
     */
    public function getMissingKeys(array $keys1, array $keys2): array
    {
        return array_values(array_diff($keys1, $keys2));
    }
}
