<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Functions;
use Core\Database\Join;
use Core\Database\Query;

it('generates a grouped query', function (Functions|string $column, Functions|array|string $groupBy, string $rawGroup) {
    $query = new Query();

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
