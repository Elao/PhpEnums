<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Elao\Enum\Bridge\Symfony\Bundle\ElaoEnumBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Kernel for tests.
 */
class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        return array_filter([
            new FrameworkBundle(),
            new DoctrineBundle(),
            class_exists(DoctrineMongoDBBundle::class) ? new DoctrineMongoDBBundle() : null,
            new ElaoEnumBundle(),
        ]);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getProjectDir() . '/config/config.yml');

        if (class_exists(DoctrineMongoDBBundle::class)) {
            $loader->load($this->getProjectDir() . '/config/mongodb.yml');
        }
    }

    public function getProjectDir(): string
    {
        return __DIR__ . '/../';
    }
}
