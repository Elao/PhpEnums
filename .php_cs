<?php

$header = <<<'EOF'
This file is part of the "elao/enum" package.

Copyright (C) Elao

@author Elao <contact@elao.com>
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tests/Fixtures/Integration/Symfony/var')
    ->exclude('tests/Fixtures/Bridge/Doctrine/DBAL/Types/TypesDumperTest')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'php_unit_namespaced' => true,
        'psr0' => false,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_summary' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_order' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'simplified_null_return' => false,
        'header_comment' => ['header' => $header],
        'yoda_style' => null,
        'no_unneeded_final_method' => false, // final private __construct is a valid use-case
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'single_line_throw' => false,
    ])
;
