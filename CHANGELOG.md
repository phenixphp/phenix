# Phenix PHP release notes

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

# Release Notes for 0.4.x

## [v0.4.1 (2024-12-24)](https://github.com/phenixphp/phenix/compare/0.4.0...0.4.1)

### Changed
- Add paths to watch in hot reloading. ([#75](https://github.com/phenixphp/phenix/pull/75))

## [v0.4.0 (2024-12-20)](https://github.com/phenixphp/phenix/compare/0.3.8...0.4.0)

## Added
- Start server with file watcher. ([#72](https://github.com/phenixphp/phenix/pull/72))
- CORS and validation layer. ([#71](https://github.com/phenixphp/phenix/pull/71))

# Release Notes for 0.3.x

## [v0.3.8 (2023-10-10)](https://github.com/phenixphp/phenix/compare/0.3.7...0.3.8)

### Changed
- Bump `phenix` framework to version `0.3.8`. ([#68](https://github.com/phenixphp/phenix/pull/68))

## [v0.3.7 (2023-10-10)](https://github.com/phenixphp/phenix/compare/0.3.6...0.3.7)

### Changed
- Rename bootstrap file. ([#67](https://github.com/phenixphp/phenix/pull/67))
- Bump `phenix` framework to version `0.3.7`. ([#67](https://github.com/phenixphp/phenix/pull/67))

## [v0.3.6 (2023-10-07)](https://github.com/phenixphp/phenix/compare/0.3.5...0.3.6)

### Changed
- Bump `phenix` framework to version `0.3.6`. ([#64](https://github.com/phenixphp/phenix/pull/64))

## [v0.3.5 (2023-10-06)](https://github.com/phenixphp/phenix/compare/0.3.4...0.3.5)

### Changed
- Bump `phenix` framework to version `0.3.5`. ([#61](https://github.com/phenixphp/phenix/pull/61))

## [v0.3.4 (2023-10-06)](https://github.com/phenixphp/phenix/compare/0.3.3...0.3.4)

### Changed
- Bump `phenix` framework to version `0.3.4`. ([#58](https://github.com/phenixphp/phenix/pull/58))

## [v0.3.3 (2023-10-06)](https://github.com/phenixphp/phenix/compare/0.3.2...0.3.3)

### Changed
- Bump `phenix` framework to version `0.3.3`. ([#55](https://github.com/phenixphp/phenix/pull/55))

## [v0.3.2 (2023-10-05)](https://github.com/phenixphp/phenix/compare/0.2.1...0.3.2)

### Changed
- Framework as separate repository. ([#52](https://github.com/phenixphp/phenix/pull/52))

# Release Notes for 0.1.x

## [v0.2.1 (2023-09-30)](https://github.com/phenixphp/phenix/compare/0.2.0...0.2.1)

### Fixed
- Ensure dabatase directory exists before create migration. ([49](https://github.com/phenixphp/phenix/pull/49))

## [v0.2.0 (2023-09-29)](https://github.com/phenixphp/phenix/compare/0.1.0...0.2.0)

### Added
- Add `paginate` method to the query builder. ([42](https://github.com/phenixphp/phenix/pull/42))
- Add `count` method to the query builder. ([42](https://github.com/phenixphp/phenix/pull/42))
- Add `insert` method to the query builder. ([43](https://github.com/phenixphp/phenix/pull/43))
- Add `exists` and `doesntExists` methods to the query builder. ([#44](https://github.com/phenixphp/phenix/pull/44))
- Add `delete` method to the query builder. ([#45](https://github.com/phenixphp/phenix/pull/45))

### Changed
- Load routes before server running. ([#41](https://github.com/phenixphp/phenix/pull/41))
- Load custom environment files. ([#40](https://github.com/phenixphp/phenix/pull/40))
- Improve service provider structure. ([#38](https://github.com/phenixphp/phenix/pull/38))
- Improve class API to `\Phenix\Database\QueryGenerator`, now it has final methods. ([#44](https://github.com/phenixphp/phenix/pull/44))

### Fixed
- Apply provides in database service provider. ([#46](https://github.com/phenixphp/phenix/pull/46))

## [v0.1.0 (2023-09-15)](https://github.com/phenixphp/phenix/compare/0.1.0...0.0.1-alpha.1)

### Added
- Migrations and seeder support. ([#35](https://github.com/phenixphp/phenix/pull/35))
- Basic query builder ([#33](https://github.com/phenixphp/phenix/pull/33))
- Routes with support for groups ([#28](https://github.com/phenixphp/phenix/pull/28))
- Ability to use multiple logger channels. ([#24](https://github.com/phenixphp/phenix/pull/24))
- Command to make middlewares. ([#19](https://github.com/phenixphp/phenix/pull/19))
- SonarCloud integration. ([#13](https://github.com/phenixphp/phenix/pull/13))
- PHPInsights integration. ([#12](https://github.com/phenixphp/phenix/pull/12))
- PHPStan integration. ([#11](https://github.com/phenixphp/phenix/pull/11))
- GitHub actions integration. ([#10](https://github.com/phenixphp/phenix/pull/10))
- Command to make test `make:test`. ([#9](https://github.com/phenixphp/phenix/pull/9))
- Tests for the `make:controller` command. ([#6](https://github.com/phenixphp/phenix/pull/6))
