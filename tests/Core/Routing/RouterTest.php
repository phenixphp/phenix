<?php

declare(strict_types=1);

namespace Tests\Core\Runtime;

use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\AcceptJsonResponses;
use Core\Constants\Http\Methods;
use Core\Routing\Route;
use Tests\Util\RouteParser;

it('adds get routes successfully', function (string $method, Methods $httpMethod) {
    $router = new Route();

    $router->{$method}('/', fn () => 'Hello')
        ->name('awesome')
        ->middleware([
            AcceptJsonResponses::class,
        ]);

    $parser = new RouteParser($router);

    $parser->assertMethodIs($httpMethod)
        ->assertNameIs('awesome')
        ->assertHasMiddlewares([AcceptJsonResponses::class]);
})->with([
    ['get', Methods::GET],
    ['post', Methods::POST],
    ['put', Methods::PUT],
    ['patch', Methods::PATCH],
    ['delete', Methods::DELETE],
]);

it('adds get routes with params successfully', function () {
    $router = new Route();

    $router->get('/users/{user}', fn () => 'Hello')
        ->name('users.show');

    $parser = new RouteParser($router);

    $parser->assertMethodIs(Methods::GET)
        ->assertNameIs('users.show')
        ->assertContainsParameters(['user']);
});

it('adds get routes with many params successfully', function () {
    $router = new Route();

    $router->get('/users/{user}/posts/{post}', fn () => 'Hello')
        ->name('users.posts.show');

    $parser = new RouteParser($router);

    $parser->assertMethodIs(Methods::GET)
        ->assertNameIs('users.posts.show')
        ->assertContainsParameters(['user', 'post']);
});

it('can call a class callable method', function () {
    $router = new Route();

    $router->get('/users/{user}/posts/{post}', [WelcomeController::class, 'index'])
        ->name('users.posts.show');

    $parser = new RouteParser($router);

    $parser->assertMethodIs(Methods::GET)
        ->assertNameIs('users.posts.show')
        ->assertContainsParameters(['user', 'post']);
});

it('can add route group from middleware method', function () {
    $router = new Route();

    $router->middleware(AcceptJsonResponses::class)
        ->name('admin')
        ->prefix('admin')
        ->group(function (Route $route) {
            $route->get('users', fn () => 'User index')
                ->name('users.index');

            $route->get('users/{user}', fn () => 'User details')
                ->name('users.show');

            $route->name('accounting')
                ->prefix('accounting')
                ->group(function (Route $route) {
                    $route->get('invoices', fn () => 'Invoice index')
                        ->name('invoices.index');
                });
        });

    $router->get('products', fn () => 'Product index')
        ->name('products.index')
        ->middleware(AcceptJsonResponses::class);

    $expectedRoutes = [
        [
            'method' => Methods::GET,
            'uri' => '/admin/users',
            'middlewares' => [new AcceptJsonResponses()],
            'name' => 'admin.users.index',
        ],
        [
            'method' => Methods::GET,
            'uri' => '/admin/users/{user}',
            'middlewares' => [new AcceptJsonResponses()],
            'name' => 'admin.users.show',
        ],
        [
            'method' => Methods::GET,
            'uri' => '/admin/accounting/invoices',
            'middlewares' => [new AcceptJsonResponses()],
            'name' => 'admin.accounting.invoices.index',
        ],
        [
            'method' => Methods::GET,
            'uri' => '/products',
            'middlewares' => [new AcceptJsonResponses()],
            'name' => 'products.index',
        ],
    ];

    $routes = $router->toArray();

    foreach ($expectedRoutes as $index => $expectedRoute) {
        [$method, $uri,, $middlewares, $name] = $routes[$index];

        expect($method)->toBe($expectedRoute['method']);
        expect($uri)->toBe($expectedRoute['uri']);
        expect($middlewares)->toMatchArray($expectedRoute['middlewares']);
        expect($name)->toBe($expectedRoute['name']);
    }
});
