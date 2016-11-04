<?php

$header = <<<'EOF'
This file is part of the "elao/enum" package.

Copyright (C) 2016 Elao

@author Elao <contact@elao.com>
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(__DIR__ . '/tests/Fixtures/Functional/Symfony/app/cache')
;

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->finder($finder)
    ->setRules([
        '@Symfony' => true,
        'psr0' => false,
        'concat_without_spaces' => false,
        'concat_with_spaces' => true,
        'phpdoc_short_description' => false,
        'phpdoc_order' => true,
        'short_array_syntax' => true,
        'ordered_imports' => true,
        'simplified_null_return' => false,
        'header_comment' => [
            'header' => $header,
        ],
    ])
;
