<?php

declare(strict_types=1);

use Core\Database\Paginator;
use Core\Util\URL;
use League\Uri\Uri;
use Core\Data\Collection;

it('calculates pagination data', function () {
    $uri = Uri::new(URL::build('users', ['page' => 1, 'per_page' => 15]));

    $paginator = new Paginator($uri, new Collection('array'), 50, 15, 1);

    expect($paginator->data()->toArray())->toBe([]);
    expect($paginator->total())->toBe(50);
    expect($paginator->lastPage())->toBe(4);
    expect($paginator->currentPage())->toBe(1);
    expect($paginator->lastPage())->toBe(4);
    expect($paginator->hasPreviousPage())->toBeFalse();
    expect($paginator->hasNextPage())->toBeTrue();
    expect($paginator->from())->toBe(1);
    expect($paginator->to())->toBe(15);

    $links = array_map(function (int $page) {
        return [
            'url' => URL::build('users', ['page' => $page, 'per_page' => 15]),
            'label' => $page
        ];
    }, [1, 2, 3, 4]);

    expect($paginator->toArray())->toBe([
        'path' => URL::build('users'),
        'current_page' => 1,
        'last_page' => 4,
        'per_page' => 15,
        'total' => 50,
        'first_page_url' => URL::build('users', ['page' => 1, 'per_page' => 15]),
        'last_page_url' => URL::build('users', ['page' => 4, 'per_page' => 15]),
        'prev_page_url' => null,
        'next_page_url' => URL::build('users', ['page' => 2, 'per_page' => 15]),
        'from' => 1,
        'to' => 15,
        'data' => [],
        'links' => $links,
    ]);
});

it('calculates pagination data in custom page', function (
    int $currentPage,
    string|null $prevUrl,
    string|null $nextUrl,
    int $from,
    int $to
) {
    $uri = Uri::new(URL::build('users', ['page' => 1, 'per_page' => 15]));

    $paginator = new Paginator($uri, new Collection('array'), 50, 15, $currentPage);

    $links = array_map(function (int $page) {
        return [
            'url' => URL::build('users', ['page' => $page, 'per_page' => 15]),
            'label' => $page
        ];
    }, [1, 2, 3, 4]);

    expect($paginator->toArray())->toBe([
        'path' => URL::build('users'),
        'current_page' => $currentPage,
        'last_page' => 4,
        'per_page' => 15,
        'total' => 50,
        'first_page_url' => URL::build('users', ['page' => 1, 'per_page' => 15]),
        'last_page_url' => URL::build('users', ['page' => 4, 'per_page' => 15]),
        'prev_page_url' => $prevUrl ? URL::build('users', ['page' => $prevUrl, 'per_page' => 15]) : null,
        'next_page_url' => $nextUrl ? URL::build('users', ['page' => $nextUrl, 'per_page' => 15]) : null,
        'from' => $from,
        'to' => $to,
        'data' => [],
        'links' => $links,
    ]);
})->with([
    [1, null, 2, 1, 15],
    [2, 1, 3, 16, 30],
    [3, 2, 4, 31, 45],
    [4, 3, null, 46, 50],
]);

it('calculates pagination data with separators', function (
    array $dataset,
    int $currentPage,
    string|null $prevUrl,
    string|null $nextUrl,
    int $from,
    int $to
) {
    $uri = Uri::new(URL::build('users', ['page' => $currentPage, 'per_page' => 15]));

    $paginator = new Paginator($uri, new Collection('array'), 150, 15, $currentPage);

    $links = array_map(function (string|int $page) {
        $url = \is_string($page)
            ? null
            : URL::build('users', ['page' => $page, 'per_page' => 15]);

        return [
            'url' => $url,
            'label' => $page
        ];
    }, $dataset);
    // dump($links, $paginator->links());
    expect($paginator->toArray())->toBe([
        'path' => URL::build('users'),
        'current_page' => $currentPage,
        'last_page' => 10,
        'per_page' => 15,
        'total' => 150,
        'first_page_url' => URL::build('users', ['page' => 1, 'per_page' => 15]),
        'last_page_url' => URL::build('users', ['page' => 10, 'per_page' => 15]),
        'prev_page_url' => $prevUrl ? URL::build('users', ['page' => $prevUrl, 'per_page' => 15]) : null,
        'next_page_url' => $nextUrl ? URL::build('users', ['page' => $nextUrl, 'per_page' => 15]) : null,
        'from' => $from,
        'to' => $to,
        'data' => [],
        'links' => $links,
    ]);
})->with([
    [[1, 2, 3, 4, 5, '...', 10], 1, null, 2, 1, 15],
    [[1, 2, 3, 4, 5, '...', 10], 2, 1, 3, 16, 30],
    [[1, 2, 3, 4, 5, '...', 10], 3, 2, 4, 31, 45],
    [[1, 2, 3, 4, 5, '...', 10], 4, 3, 5, 46, 60],
    [[1, '...', 3, 4, 5, 6, 7, '...', 10], 5, 4, 6, 61, 75],
    [[1, '...', 4, 5, 6, 7, 8, '...', 10], 6, 5, 7, 76, 90],
    [[1, '...', 5, 6, 7, 8, 9, 10], 7, 6, 8, 91, 105],
    [[1, '...', 6, 7, 8, 9, 10], 8, 7, 9, 106, 120],
    [[1, '...', 7, 8, 9, 10], 9, 8, 10, 121, 135],
    [[1, '...', 8, 9, 10], 10, 9, null, 136, 150],
]);