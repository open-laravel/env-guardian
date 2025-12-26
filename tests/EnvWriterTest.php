<?php

use OpenLaravel\EnvGuardian\Services\EnvWriter;

it('can append keys to env file', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\n");

    $writer = new EnvWriter;
    $added = $writer->appendKeys($tempFile, ['DB_HOST', 'DB_PORT'], '');

    $content = file_get_contents($tempFile);

    expect($added)->toBe(2)
        ->and($content)->toContain('DB_HOST=')
        ->and($content)->toContain('DB_PORT=');

    unlink($tempFile);
});

it('can append keys with mask value', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\n");

    $writer = new EnvWriter;
    $writer->appendKeys($tempFile, ['DB_HOST'], 'changeme');

    $content = file_get_contents($tempFile);

    expect($content)->toContain('DB_HOST=changeme');

    unlink($tempFile);
});

it('can append keys with sorting', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\n");

    $writer = new EnvWriter;
    $writer->appendKeys($tempFile, ['ZZZ_KEY', 'AAA_KEY'], '', true);

    $content = file_get_contents($tempFile);
    $lines = explode("\n", trim($content));

    // AAA should come before ZZZ
    $aaaPosition = array_search('AAA_KEY=', $lines);
    $zzzPosition = array_search('ZZZ_KEY=', $lines);

    expect($aaaPosition)->toBeLessThan($zzzPosition);

    unlink($tempFile);
});

it('can append keys with grouping', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\n");

    $writer = new EnvWriter;
    $writer->appendKeys($tempFile, ['DB_HOST', 'DB_PORT', 'CACHE_DRIVER'], '', false, true);

    $content = file_get_contents($tempFile);

    expect($content)->toContain('# DB')
        ->and($content)->toContain('# CACHE');

    unlink($tempFile);
});

it('handles empty keys array', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'env');
    file_put_contents($tempFile, "APP_NAME=TestApp\n");

    $writer = new EnvWriter;
    $added = $writer->appendKeys($tempFile, []);

    expect($added)->toBe(0);

    unlink($tempFile);
});
