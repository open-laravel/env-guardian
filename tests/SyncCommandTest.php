<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    // Create temporary .env and .env.example files for testing
    $this->envPath = base_path('.env');
    $this->examplePath = base_path('.env.example');

    // Backup existing files if they exist
    if (file_exists($this->envPath)) {
        $this->envBackup = file_get_contents($this->envPath);
    }
    if (file_exists($this->examplePath)) {
        $this->exampleBackup = file_get_contents($this->examplePath);
    }
});

afterEach(function () {
    // Restore or clean up
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

it('can sync missing keys to env.example', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\nAPP_ENV=local\nDB_HOST=localhost\n");
    file_put_contents($this->examplePath, "APP_NAME=\nAPP_ENV=\n");

    Artisan::call('env:sync-example');

    $content = file_get_contents($this->examplePath);

    expect($content)->toContain('APP_NAME=')
        ->and($content)->toContain('APP_ENV=')
        ->and($content)->toContain('DB_HOST=');
});

it('shows success message when already in sync', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\n");
    file_put_contents($this->examplePath, "APP_NAME=\n");

    $exitCode = Artisan::call('env:sync-example');

    expect($exitCode)->toBe(0);
});

it('handles dry-run mode', function () {
    file_put_contents($this->envPath, "APP_NAME=TestApp\nDB_HOST=localhost\n");
    file_put_contents($this->examplePath, "APP_NAME=\n");

    $originalContent = file_get_contents($this->examplePath);

    Artisan::call('env:sync-example', ['--dry-run' => true]);

    $newContent = file_get_contents($this->examplePath);

    expect($newContent)->toBe($originalContent);
});

it('fails when env file does not exist', function () {
    if (file_exists($this->envPath)) {
        unlink($this->envPath);
    }

    $exitCode = Artisan::call('env:sync-example');

    expect($exitCode)->toBe(1);
});
