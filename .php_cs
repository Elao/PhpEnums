<?php

$header = <<<'EOF'
This file is part of the "elao/enum" package.

Copyright (C) 2016 Elao

@author Elao <contact@elao.com>
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->exclude([__DIR__ . '/tests/fixtures'])
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-psr0',
        '-concat_without_spaces',
        '-phpdoc_short_description',
        'concat_with_spaces',
        'header_comment',
        'ordered_use',
        'phpdoc_order',
        'short_array_syntax',
    ])
    ->setUsingCache(true)
    ->finder($finder)
;
