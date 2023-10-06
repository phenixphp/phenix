<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']],
    'global_namespace_import' => ['import_classes' => true, 'import_constants' => false, 'import_functions' => true],
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline' => true,
    'phpdoc_scalar' => true,
    'unary_operator_spaces' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_var_without_name' => true,
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
        ],
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'single_trait_insert_per_statement' => true,
];

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config)
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);