# Env Guardian

[![Latest Version on Packagist](https://img.shields.io/packagist/v/open-laravel/env-guardian.svg?style=flat-square)](https://packagist.org/packages/open-laravel/env-guardian)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/open-laravel/env-guardian/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/open-laravel/env-guardian/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/open-laravel/env-guardian.svg?style=flat-square)](https://packagist.org/packages/open-laravel/env-guardian)

**Env Guardian** helps Laravel teams keep `.env.example` in sync with `.env` files - safely and non-destructively.

## Features

✅ **Safe & Non-Destructive** - Never removes or overwrites existing keys  
✅ **Git Hook Integration** - Non-blocking pre-commit warnings  
✅ **Multiple Commands** - Sync, diff, check, discover env keys  
✅ **CI/CD Ready** - Perfect for automated checks  
✅ **Laravel 9+** - Supports Laravel 9, 10, 11, and 12  
✅ **PHP 8.1+** - Modern PHP support

## Installation

You can install the package via composer:

```bash
composer require open-laravel/env-guardian
```

Publish the config file:

```bash
php artisan vendor:publish --tag=env-guardian-config
```

This will create a `config/env-guardian.php` file with the following options:

```php
return [
    'required_keys' => [],           // Keys that must be present in .env
    'mask_values' => true,           // Whether to mask values in .env.example
    'mask_with' => '',               // Mask value (empty or 'changeme')
    'grouped' => false,              // Group keys by prefix
    'ci_fail_on_error' => true,      // Fail in CI mode if required keys missing
];
```

## Usage

### 1. Sync Missing Keys

Sync keys from `.env` to `.env.example`:

```bash
php artisan env:sync-example
```

**Options:**

- `--dry-run` - Preview changes without writing
- `--sort` - Sort keys alphabetically
- `--grouped` - Group keys with comment headers
- `--mask-value=` - Custom mask value (default: empty)

**Example:**

```bash
# Preview changes
php artisan env:sync-example --dry-run

# Sync and sort keys
php artisan env:sync-example --sort

# Sync with grouped output
php artisan env:sync-example --grouped

# Use custom mask value
php artisan env:sync-example --mask-value=changeme
```

### 2. Compare Env Files

Compare `.env` and `.env.example` to see differences:

```bash
php artisan env:diff
```

**Options:**

- `--quiet` - Minimal output for CI/scripting

**Example:**

```bash
# Show full diff
php artisan env:diff

# Quiet mode for scripts
php artisan env:diff --quiet
```

### 3. Check Required Keys

Check if required environment keys are present:

```bash
php artisan env:check
```

**Options:**

- `--ci` - Exit with non-zero code if keys are missing (for CI/CD)

**Example:**

```bash
# Check required keys
php artisan env:check

# CI mode
php artisan env:check --ci
```

Configure required keys in `config/env-guardian.php`:

```php
'required_keys' => [
    'APP_KEY',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_DATABASE',
],
```

### 4. Install Git Hook

Install a non-blocking pre-commit hook:

```bash
php artisan env:install-hook
```

**Options:**

- `--force` - Overwrite existing hook
- `--uninstall` - Remove the hook

The hook will:
- ✅ Warn about missing keys in `.env.example`
- ✅ **NOT** block commits (always exits 0)
- ✅ Suggest running `env:sync-example`

**Example:**

```bash
# Install hook
php artisan env:install-hook

# Force overwrite existing hook
php artisan env:install-hook --force

# Uninstall hook
php artisan env:install-hook --uninstall
```

### 5. Discover Env Usage

Scan your code for `env()` usage:

```bash
php artisan env:discover
```

**Options:**

- `--write` - Write discovered keys to `.env.example`
- `--mask-value=` - Custom mask value

This command scans:
- `config/` directory
- `app/` directory
- `routes/` directory

**Example:**

```bash
# Discover keys
php artisan env:discover

# Discover and write to .env.example
php artisan env:discover --write
```

## CI/CD Integration

### GitHub Actions

```yaml
name: Check Environment

on: [push, pull_request]

jobs:
  env-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - run: composer install
      - run: php artisan env:check --ci
      - run: php artisan env:diff --quiet
```

### GitLab CI

```yaml
env-check:
  script:
    - composer install
    - php artisan env:check --ci
    - php artisan env:diff --quiet
```

## Best Practices

1. **Always run `env:sync-example`** before committing
2. **Use `env:check --ci`** in your CI pipeline
3. **Install the Git hook** for automatic warnings
4. **Run `env:discover`** periodically to catch new env usage
5. **Configure required keys** for critical environment variables

## How It Works

### Parsing

- Reads `.env` and `.env.example` files directly from filesystem
- Parses `KEY=VALUE` pairs manually (does NOT use `env()` helper)
- Ignores comments (`#`) and empty lines
- Supports quoted values (`"value"` or `'value'`)
- Handles `export KEY=value` syntax

### Syncing

- **NEVER removes** keys from `.env.example`
- **NEVER overwrites** existing keys
- Appends new keys at the bottom
- Preserves all comments and formatting
- Idempotent (running twice produces no changes)

### Git Hook

- Installed in `.git/hooks/pre-commit`
- **Non-blocking** (always exits 0)
- Shows warning if keys are missing
- Suggests running `env:sync-example`
- Can be removed anytime

## Example Workflow

```bash
# 1. Add new env var to .env
echo "NEW_API_KEY=secret123" >> .env

# 2. Check what's different
php artisan env:diff

# 3. Sync to .env.example
php artisan env:sync-example

# 4. Commit changes
git add .env.example
git commit -m "Add NEW_API_KEY to env.example"
```

## Testing

```bash
composer test
```

## Code Quality

```bash
# Format code
composer format

# Static analysis
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Open Laravel](https://github.com/open-laravel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

