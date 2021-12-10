<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

date_default_timezone_set('UTC');

$loader = require __DIR__ . '/../vendor/autoload.php';

// Should update expectations files (api outputs, dumps, ...) automatically or not.
\define('UPDATE_EXPECTATIONS', filter_var(getenv('UPDATE_EXPECTATIONS') ?: getenv('UP'), FILTER_VALIDATE_BOOLEAN));

return $loader;
