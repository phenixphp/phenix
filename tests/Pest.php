<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Core\Constants\Http;
use Core\Facades\Config;
use Tests\Util\TestResponse;

uses(Tests\TestCase::class)->in('Core');
// uses(Tests\TestCase::class)->in('Unit');
// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function call(
    string $method,
    string $path,
    array $parameters = [],
    array|string|null $body = null,
    array $headers = []
): TestResponse {
    $path = trim($path, "/");

    $port = Config::get('app.port');

    [$ipv4,] = Config::get('app.url');

    $uri = "http://{$ipv4}:{$port}/{$path}";

    if (! empty($parameters)) {
        $uri .= '?' . http_build_query($parameters);
    }

    $request = new Request($uri, $method);

    if (! empty($headers)) {
        $request->setHeaders($headers);
    }

    if (! empty($body)) {
        $body = \is_array($body) ? json_encode($body) : $body;

        $request->setBody($body);
    }

    $client = HttpClientBuilder::buildDefault();

    return new TestResponse($client->request($request));
}

function get(string $path, array $parameters = [], array $headers = []): TestResponse
{
    return call(method: Http::METHOD_GET, path: $path, parameters: $parameters, headers: $headers);
}

function post(string $path, array|string|null $body, array $parameters = [], array $headers = []): TestResponse
{
    return call(Http::METHOD_POST, $path, $parameters, $body, $headers);
}

function put(string $path, array|string|null $body, array $parameters = [], array $headers = []): TestResponse
{
    return call(Http::METHOD_PUT, $path, $parameters, $body, $headers);
}

function patch(string $path, array|string|null $body, array $parameters = [], array $headers = []): TestResponse
{
    return call(Http::METHOD_PATCH, $path, $parameters, $body, $headers);
}

function delete(string $path, array $parameters = [], array $headers = []): TestResponse
{
    return call(method: Http::METHOD_DELETE, path: $path, parameters: $parameters, headers: $headers);
}
