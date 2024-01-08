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

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
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

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/config/config.yaml');

        // TODO: we can remove when 5.4 is dropped
        if (InstalledVersions::satisfies(new VersionParser(), 'doctrine/doctrine-bundle', '>=2.10')) {
            $loader->load($this->getProjectDir() . '/config/doctrine-new.yaml');
        } else {
            $loader->load($this->getProjectDir() . '/config/doctrine-old.yaml');
        }

        // TODO: we can remove when 5.4 and 6.3 is dropped
        if (InstalledVersions::satisfies(new VersionParser(), 'symfony/http-kernel', '>=6.4')) {
            $loader->load($this->getProjectDir() . '/config/config-routing-attribute.yaml');
        } else {
            $loader->load($this->getProjectDir() . '/config/config-routing-annotation.yaml');
        }

        if (str_starts_with($_ENV['DOCTRINE_DBAL_URL'], 'pdo-mysql:')) {
            $loader->load($this->getProjectDir() . '/config/mysql.yaml');
        }

        if (class_exists(DoctrineMongoDBBundle::class)) {
            $loader->load($this->getProjectDir() . '/config/mongodb.yaml');
        }
    }

    public function getProjectDir(): string
    {
        return __DIR__ . '/../';
    }
}
