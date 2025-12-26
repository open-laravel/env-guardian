# Changelog

All notable changes to `env-guardian` will be documented in this file.

## 1.0.0 - Initial Release

### Added
- `env:sync-example` command to sync missing keys from .env to .env.example
  - Support for `--dry-run`, `--sort`, `--grouped`, and `--mask-value` options
  - Safe, non-destructive operations that never remove or overwrite existing keys
- `env:diff` command to compare .env and .env.example files
  - Shows keys missing in .env.example and keys missing in .env
  - Support for `--quiet` mode for CI/scripting
- `env:check` command to validate required environment keys
  - Configurable required keys in config file
  - `--ci` mode that exits with non-zero code on missing keys
- `env:install-hook` command to install non-blocking Git pre-commit hook
  - Warns about missing keys but never blocks commits
  - Support for `--force` and `--uninstall` options
- `env:discover` command to scan codebase for env() usage
  - Scans config/, app/, and routes/ directories
  - Support for `--write` flag to add discovered keys to .env.example
- Comprehensive configuration file with options for:
  - Required keys validation
  - Value masking
  - Grouped output
  - CI failure behavior
- Full test coverage for all services and commands
- Architecture tests to ensure code quality
- Support for Laravel 9, 10, 11, and 12
- Support for PHP 8.1+

### Services
- `EnvParser` - Safely parse .env files without using env() helper
- `EnvComparator` - Compare key sets between environment files
- `EnvWriter` - Append keys safely with support for sorting and grouping
- `GitHookInstaller` - Install and manage pre-commit Git hooks
- `EnvScanner` - Discover env() usage in codebase

