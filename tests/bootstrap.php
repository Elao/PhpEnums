<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
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

// Prepare integration tests:
require __DIR__ . '/Fixtures/Integration/Symfony/src/AppKernel.php';

// Empty generated symfony cache
(new Filesystem())->remove(__DIR__ . '/Fixtures/Integration/var/cache');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$kernel = new AppKernel('test', true);
$kernel->boot();
$doctrine = $kernel->getContainer()->get('doctrine');
$connectionName = $doctrine->getDefaultConnectionName();
$managerName = $doctrine->getDefaultManagerName();
$manager = $doctrine->getManager($managerName);
$schemaTool = new SchemaTool($manager);

$schemaTool->dropDatabase();

$application = new Application($kernel);
$application->setAutoExit(false);

// Create database
$input = new ArrayInput(['command' => 'doctrine:database:create']);
$application->run($input, new NullOutput());
// Create database schema
$input = new ArrayInput(['command' => 'doctrine:schema:create']);
$application->run($input, new NullOutput());

return $loader;
