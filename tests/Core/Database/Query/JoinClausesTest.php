<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Constants\Joins;
use Core\Database\Join;
use Core\Database\Query;

it('generates query with joins', function (string $method, string $joinType) {
    $query = new Query();

    $sql = $query->select([
            'products.id',
            'products.description',
            'categories.description',
        ])
        ->from('products')
        ->{$method}('categories', function (Join $join) {
            $join->onEqual('products.category_id', 'categories.id');
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT products.id, products.description, categories.description "
        . "FROM products "
        . "{$joinType} categories "
        . "ON products.category_id = categories.id";

    expect($dml)->toBe($expected);
    expect($params)->toBeEmpty();
})->with([
    ['innerJoin', Joins::INNER->value],
    ['leftJoin', Joins::LEFT->value],
    ['leftOuterJoin', Joins::LEFT_OUTER->value],
    ['rightJoin', Joins::RIGHT->value],
    ['rightOuterJoin', Joins::RIGHT_OUTER->value],
    ['crossJoin', Joins::CROSS->value],
]);
