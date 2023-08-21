<?php

declare(strict_types=1);

namespace Tests\Core\Database\QueryGenerator;

use Core\Database\Functions;
use Core\Database\Join;
use Core\Database\QueryGenerator;

it('generates a grouped query', function (Functions|string $column, Functions|array|string $groupBy, string $rawGroup) {
    $query = new QueryGenerator();

    $sql = $query->select([
            $column,
            'products.category_id',
            'categories.description',
        ])
        ->from('products')
        ->leftJoin('categories', function (Join $join) {
            $join->onEqual('products.category_id', 'categories.id');
        })
        ->groupBy($groupBy)
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT {$column}, products.category_id, categories.description "
        . "FROM products "
        . "LEFT JOIN categories ON products.category_id = categories.id "
        . "GROUP BY {$rawGroup}";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    [Functions::count('products.id'), 'category_id', 'category_id'],
    ['location_id', ['category_id', 'location_id'], 'category_id, location_id'],
    [Functions::count('products.id'), Functions::count('products.id'), 'COUNT(products.id)'],
]);

it('generates a grouped and ordered query', function (
    Functions|string $column,
    Functions|array|string $groupBy,
    string $rawGroup
) {
    $query = new QueryGenerator();

    $sql = $query->select([
            $column,
            'products.category_id',
            'categories.description',
        ])
        ->from('products')
        ->leftJoin('categories', function (Join $join) {
            $join->onEqual('products.category_id', 'categories.id');
        })
        ->groupBy($groupBy)
        ->orderBy('products.id')
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT {$column}, products.category_id, categories.description "
        . "FROM products "
        . "LEFT JOIN categories ON products.category_id = categories.id "
        . "GROUP BY {$rawGroup} "
        . "ORDER BY products.id DESC";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    [Functions::count('products.id'), 'category_id', 'category_id'],
    ['location_id', ['category_id', 'location_id'], 'category_id, location_id'],
    [Functions::count('products.id'), Functions::count('products.id'), 'COUNT(products.id)'],
]);
