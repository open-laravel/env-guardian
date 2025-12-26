<?php

use OpenLaravel\EnvGuardian\Services\EnvParser;

it('can parse env file', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\nAPP_ENV=local\n");

    $parser = new EnvParser;
    $result = $parser->parse($tempFile);

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('APP_NAME')
        ->and($result['APP_NAME'])->toBe('TestApp')
        ->and($result['APP_ENV'])->toBe('local');

    unlink($tempFile);
});

it('can get keys from env file', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\nAPP_ENV=local\n");

    $parser = new EnvParser;
    $keys = $parser->getKeys($tempFile);

    expect($keys)->toBeArray()
        ->and($keys)->toContain('APP_NAME')
        ->and($keys)->toContain('APP_ENV');

    unlink($tempFile);
});

it('returns empty array for non-existent file', function () {
    $parser = new EnvParser;
    $result = $parser->parse('/non/existent/file');

    expect($result)->toBeArray()
        ->and($result)->toBeEmpty();
});

it('skips comments and empty lines', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "# Comment\nAPP_NAME=TestApp\n\nAPP_ENV=local\n");

    $parser = new EnvParser;
    $result = $parser->parse($tempFile);

    expect($result)->toHaveCount(2);

    unlink($tempFile);
});

it('handles export prefix', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "export APP_NAME=TestApp\nexport APP_ENV=local\n");

    $parser = new EnvParser;
    $result = $parser->parse($tempFile);

    expect($result)->toHaveKey('APP_NAME')
        ->and($result['APP_NAME'])->toBe('TestApp');

    unlink($tempFile);
});

it('handles quoted values', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=\"Test App\"\nAPP_URL='http://localhost'\n");

    $parser = new EnvParser;
    $result = $parser->parse($tempFile);

    expect($result['APP_NAME'])->toBe('Test App')
        ->and($result['APP_URL'])->toBe('http://localhost');

    unlink($tempFile);
});

