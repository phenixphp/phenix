<?php

declare(strict_types=1);

namespace Tests\Core\Database\Query;

use Core\Database\Functions;
use Core\Database\Having;
use Core\Database\Join;
use Core\Database\Query;

it('generates a query using having clause', function () {
    $query = new Query();

    $sql = $query->select([
            Functions::count('products.id')->as('identifiers'),
            'products.category_id',
            'categories.description',
        ])
        ->from('products')
        ->leftJoin('categories', function (Join $join) {
            $join->onEqual('products.category_id', 'categories.id');
        })
        ->groupBy('products.category_id')
        ->having(function (Having $having): void {
            $having->whereGreatherThan('identifiers', 5);
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT COUNT(products.id) AS identifiers, products.category_id, categories.description "
        . "FROM products "
        . "LEFT JOIN categories ON products.category_id = categories.id "
        . "GROUP BY products.category_id HAVING identifiers > ?";

    expect($dml)->toBe($expected);
    expect($params)->toBe([5]);
});

it('generates a query using having with many clauses', function () {
    $query = new Query();

    $sql = $query->select([
            Functions::count('products.id')->as('identifiers'),
            'products.category_id',
            'categories.description',
        ])
        ->from('products')
        ->leftJoin('categories', function (Join $join) {
            $join->onEqual('products.category_id', 'categories.id');
        })
        ->groupBy('products.category_id')
        ->having(function (Having $having): void {
            $having->whereGreatherThan('identifiers', 5)
                ->whereGreatherThan('products.category_id', 10);
        })
        ->toSql();

    [$dml, $params] = $sql;

    $expected = "SELECT COUNT(products.id) AS identifiers, products.category_id, categories.description "
        . "FROM products "
        . "LEFT JOIN categories ON products.category_id = categories.id "
        . "GROUP BY products.category_id HAVING identifiers > ? AND products.category_id > ?";

    expect($dml)->toBe($expected);
    expect($params)->toBe([5, 10]);
});
