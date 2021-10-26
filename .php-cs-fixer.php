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
    ->exclude('tests/Fixtures/Bridge/Doctrine/ODM/Types/TypesDumperTest')
    // Excluded until php-cs-fixer supports PHP 8 attributes:
    ->notPath('src/Bridge/Symfony/Validator/Constraint/Enum.php')
    ->notPath('tests/Fixtures/Bridge/Symfony/Validator/Constraint/ObjectWithEnumChoiceAsPhpAttribute.php')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'header_comment' => ['header' => $header],
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'no_unneeded_final_method' => false, // final private __construct is a valid use-case
        'ordered_imports' => true,
        'php_unit_namespaced' => true,
        'php_unit_method_casing' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_summary' => false,
        'phpdoc_order' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'psr_autoloading' => true,
        'single_line_throw' => false,
        'simplified_null_return' => false,
        'yoda_style' => [],
    ])
;
