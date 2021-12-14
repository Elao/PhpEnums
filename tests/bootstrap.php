<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

use Symfony\Component\Filesystem\Filesystem;

date_default_timezone_set('UTC');

$loader = require __DIR__ . '/../vendor/autoload.php';

if (file_exists($varDumper = __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/VarDumper/Resources/functions/dump.php')) {
    require_once $varDumper;
} else {
    require_once __DIR__ . '/../vendor/symfony/var-dumper/Resources/functions/dump.php';
}

// Should update expectations files (api outputs, dumps, ...) automatically or not.
\define('UPDATE_EXPECTATIONS', filter_var(getenv('UPDATE_EXPECTATIONS') ?: getenv('UP'), FILTER_VALIDATE_BOOLEAN));

// Empty generated symfony cache
(new Filesystem())->remove(__DIR__ . '/Fixtures/Integration/Symfony/var/cache');

return $loader;
