<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
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

$loader = require __DIR__ . '/../../../../../vendor/autoload.php';

require __DIR__ . '/AppKernel.php';

// Empty generated symfony cache
(new Filesystem())->remove(__DIR__ . '/cache');

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
