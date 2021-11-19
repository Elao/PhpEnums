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

const PACKAGE_ROOT_DIR = __DIR__ . '/..';
const FIXTURES_DIR = __DIR__ . '/Fixtures';

// Empty generated symfony cache
(new Filesystem())->remove(__DIR__ . '/Fixtures/Integration/Symfony/var/cache');

return $loader;
