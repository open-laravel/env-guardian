<?php

use OpenLaravel\EnvGuardian\Services\EnvComparator;

it('can compare env keys', function () {
    $comparator = new EnvComparator;

    $envKeys = ['APP_NAME', 'APP_ENV', 'DB_HOST'];
    $exampleKeys = ['APP_NAME', 'APP_ENV'];

    $result = $comparator->compare($envKeys, $exampleKeys);

    expect($result)->toHaveKey('missing_in_example')
        ->and($result)->toHaveKey('missing_in_env')
        ->and($result['missing_in_example'])->toContain('DB_HOST')
        ->and($result['missing_in_env'])->toBeEmpty();
});

it('can get missing keys', function () {
    $comparator = new EnvComparator;

    $keys1 = ['APP_NAME', 'APP_ENV', 'DB_HOST'];
    $keys2 = ['APP_NAME', 'APP_ENV'];

    $missing = $comparator->getMissingKeys($keys1, $keys2);

    expect($missing)->toContain('DB_HOST')
        ->and($missing)->toHaveCount(1);
});
