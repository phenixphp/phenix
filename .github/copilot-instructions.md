# Phenix Framework - AI Coding Instructions

## Architecture Overview

**Phenix** is an asynchronous PHP web framework built on the [AmPHP](https://amphp.org/) ecosystem. This is a **skeleton application** that depends on `phenixphp/framework` for core functionality. Official documentation can be found at [Phenix Documentation](https://phenix.omarbarbosa.com/).

### Key Components
- **App Structure**: Standard MVC with `app/` (controllers, tasks), `config/` (service configs), `routes/` (API definitions)
- **Framework Integration**: Core framework lives in `vendor/phenixphp/framework/src/` - reference this for understanding internals
- **Service Providers**: Configured in `config/app.php` - handles DI container, routing, queue, database, etc.
- **Entry Points**: `public/index.php` (HTTP server), `phenix` CLI (console commands)

## Development Workflow

### Server & Hot Reloading
```bash
composer dev                    # Starts development server with file watcher
# OR directly: php server       # Custom file watcher that restarts on changes
XDEBUG_MODE=off php public/index.php  # Direct server start (faster, no debugging)
```
- Server runs on `APP_URL:APP_PORT` (default: http://127.0.0.1:1337)
- Watches: `app/`, `config/`, `routes/`, `database/`, `composer.json`, `.env`
- Requires Node.js for chokidar file watcher
- Use `XDEBUG_MODE=off` for better performance when debugging isn't needed

### Testing
```bash
composer test                   # PHPUnit tests (XDEBUG_MODE=off)
composer test:coverage          # With coverage reports
```
- **Test Framework**: PHPUnit with custom HTTP client helpers
- **Test Structure**: `tests/Feature/` and `tests/Unit/` with shared `TestCase`
- **HTTP Testing**: Uses Amp HTTP client with helper functions: `get()`, `post()`, etc.

### Quality Tools
```bash
composer format                 # PHP CS Fixer
composer analyze                # PHPStan
```

## Queue System Architecture

**Critical Pattern**: This project implements an async task queue system with Redis/Database backends.

### Task Definition
```php
// Extend QueuableTask for background jobs
class MyTask extends QueuableTask
{
    protected int|null $maxTries = 3;  // Configure retries
    
    protected function handle(Channel $channel, Cancellation $cancellation): Result
    {
        // Async task logic here
        return Result::success('TaskName', 'Success message');
    }
}
```

### Task Dispatch
```php
// From controllers/anywhere:
MyTask::dispatch();              // Queue immediately
MyTask::enqueue()->delay(60);    // Queue with delay
MyTask::dispatchIf($condition);  // Conditional dispatch
```

### Queue Workers
```bash
./phenix queue:work redis --queue=default    # Process queue
./phenix queue:work --once                   # Process once and exit
./phenix queue:work --chunks                 # Batch processing
```

## Configuration Patterns

### Environment-Driven Config
```php
// config/ files use closures for lazy evaluation:
'driver' => env('QUEUE_DRIVER', static fn (): string => 'database'),
'timeout' => env('TIMEOUT', static fn (): int => 30),
```

### Service Provider Registration
- All providers registered in `config/app.php`
- Queue system: `QueueServiceProvider`, `TaskServiceProvider`
- Database: Supports MySQL/PostgreSQL with migrations via Phinx

## Data Layer

### Database
- **Migrations**: `database/migrations/` using Phinx (not Eloquent)
- **Configuration**: `config/database.php` supports multiple connections
- **CLI**: `./phenix` provides migration commands

### Queue Backends
- **Redis**: Uses Lua scripts for atomic operations (`LuaScripts.php`)
- **Database**: Uses tasks table with state management
- **Parallel**: AmPHP parallel processing with worker pools

## HTTP Layer

### Routing
```php
// routes/api.php - use Facade pattern:
Route::get('/', [WelcomeController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
```

### Controllers
```php
class MyController extends Controller
{
    public function index(): Response
    {
        return response()->json(['data' => $data]);
        // OR: response()->plain('text');
    }
}
```

## Key Conventions

1. **Strict Types**: Always use `declare(strict_types=1);`
2. **Static Factories**: Config uses static closures for defaults
3. **Facade Pattern**: `Phenix\Facades\*` for service access
4. **Result Objects**: Tasks return `Result::success()` or `Result::failure()`
5. **Async First**: Built for non-blocking I/O with AmPHP primitives

## Critical Files to Reference

- `config/app.php` - Service provider registration and app config
- `vendor/phenixphp/framework/src/Queue/` - Queue implementation details
- `vendor/phenixphp/framework/src/Tasks/QueuableTask.php` - Base task class
- `bootstrap/app.php` - Application bootstrap via `AppBuilder`

## Common Pitfalls

1. **Queue Retries**: Tasks need explicit `$maxTries` property set
2. **Environment**: Copy `.env.example` to `.env` for local development
3. **CLI vs HTTP**: Different entry points (`phenix` vs `public/index.php`)
4. **Dependencies**: Framework code lives in vendor - check there for implementation details
