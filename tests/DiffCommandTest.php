<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->envPath = base_path('.env');
    $this->examplePath = base_path('.env.example');

    // Backup existing files
    if (file_exists($this->envPath)) {
        $this->envBackup = file_get_contents($this->envPath);
    }
    if (file_exists($this->examplePath)) {
        $this->exampleBackup = file_get_contents($this->examplePath);
    }
});

afterEach(function () {
    // Restore
    if (isset($this->envBackup)) {
        file_put_contents($this->envPath, $this->envBackup);
    } elseif (file_exists($this->envPath)) {
        unlink($this->envPath);
    }

    if (isset($this->exampleBackup)) {
        file_put_contents($this->examplePath, $this->exampleBackup);
    } elseif (file_exists($this->examplePath)) {
        unlink($this->examplePath);
    }
});

it('can compare env files', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\nDB_HOST=localhost\n");
    file_put_contents($this->examplePath, "APP_NAME=\n");

    $exitCode = Artisan::call('env:diff');

    expect($exitCode)->toBe(0);
});

it('shows success when files are in sync', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\n");
    file_put_contents($this->examplePath, "APP_NAME=\n");

    $exitCode = Artisan::call('env:diff');

    expect($exitCode)->toBe(0);
});

it('handles quiet mode', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\nDB_HOST=localhost\n");
    file_put_contents($this->examplePath, "APP_NAME=\n");

    $exitCode = Artisan::call('env:diff', ['--quiet' => true]);

    expect($exitCode)->toBe(0);
});
