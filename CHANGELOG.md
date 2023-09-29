# Phenix PHP release notes

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Add `paginate` method to the query builder. ([42](https://github.com/barbosa89/phenix/pull/42))
- Add `count` method to the query builder. ([42](https://github.com/barbosa89/phenix/pull/42))
- Add `insert` method to the query builder. ([43](https://github.com/barbosa89/phenix/pull/43))
- Add `exists` and `doesntExists` methods to the query builder. ([#44](https://github.com/barbosa89/phenix/pull/44))

### Changed
- Load routes before server running. ([#41](https://github.com/barbosa89/phenix/pull/41))
- Load custom environment files. ([#40](https://github.com/barbosa89/phenix/pull/40))
- Improve service provider structure. ([#38](https://github.com/barbosa89/phenix/pull/38))
- Improve class API to `\Core\Database\QueryGenerator`, now it has final methods. ([#44](https://github.com/barbosa89/phenix/pull/44))

# Release Notes for 0.1.x

## [v0.1.0 (2023-09-15)](https://github.com/barbosa89/phenix/compare/0.1.0...0.0.1-alpha.1)

### Added
- Migrations and seeder support. ([#35](https://github.com/barbosa89/phenix/pull/35))
- Basic query builder ([#33](https://github.com/barbosa89/phenix/pull/33))
- Routes with support for groups ([#28](https://github.com/barbosa89/phenix/pull/28))
- Ability to use multiple logger channels. ([#24](https://github.com/barbosa89/phenix/pull/24))
- Command to make middlewares. ([#19](https://github.com/barbosa89/phenix/pull/19))
- SonarCloud integration. ([#13](https://github.com/barbosa89/phenix/pull/13))
- PHPInsights integration. ([#12](https://github.com/barbosa89/phenix/pull/12))
- PHPStan integration. ([#11](https://github.com/barbosa89/phenix/pull/11))
- GitHub actions integration. ([#10](https://github.com/barbosa89/phenix/pull/10))
- Command to make test `make:test`. ([#9](https://github.com/barbosa89/phenix/pull/9))
- Tests for the `make:controller` command. ([#6](https://github.com/barbosa89/phenix/pull/6))
