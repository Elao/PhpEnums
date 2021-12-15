<?php

$header = <<<'EOF'
This file is part of the "elao/enum" package.

Copyright (C) Elao

@author Elao <contact@elao.com>
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tests/Fixtures/Integration/Symfony/var')
    ->exclude('tests/Unit/Bridge/Doctrine/DBAL/Types/TypesDumperTest')
    // Enum ignored for now since php-cs-fixer removes the traits & extra blank lines:
    ->notPath([
        'tests/Fixtures/Enum/SuitWithAttributesMissingLabel.php',
        'tests/Fixtures/Enum/Suit.php',
        'tests/Fixtures/Enum/SuitWithAttributesMissingAttribute.php',
        'tests/Fixtures/Enum/SuitWithAttributes.php',
        'tests/Fixtures/Integration/Symfony/src/Enum/Suit.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'header_comment' => ['header' => $header],
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'no_unneeded_final_method' => false, // final private __construct is a valid use-case
        'ordered_imports' => true,
        'php_unit_namespaced' => true,
        'php_unit_method_casing' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_to_comment' => ['ignored_tags' => [
            // https://github.com/phpstan/phpstan/issues/5465
            'use',
        ]],
        'phpdoc_summary' => false,
        'phpdoc_order' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'psr_autoloading' => true,
        'single_line_throw' => false,
        'simplified_null_return' => false,
        'yoda_style' => [],
    ])
;
