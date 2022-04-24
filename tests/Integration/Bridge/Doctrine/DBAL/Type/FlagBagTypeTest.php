<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Doctrine\DBAL\Type;

use App\Entity\AccessRight;
use App\Enum\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Elao\Enum\FlagBag;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlagBagTypeTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    protected function tearDown(): void
    {
        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testFlagBagType(): void
    {
        $permissions = FlagBag::from(Permissions::Execute);

        $this->em->persist(new AccessRight($uuid = 'access01', $permissions));
        $this->em->flush();
        $this->em->clear();

        /** @var AccessRight $card */
        $card = $this->em->find(AccessRight::class, $uuid);

        self::assertSame([Permissions::Execute], $card->getPermissions()->getFlags());
    }

    public function testFlagBagTypeOnNullFromPHP(): void
    {
        $this->em->persist(new AccessRight($uuid = 'access01', null));
        $this->em->flush();
        $this->em->clear();

        self::assertSame(
            ['permissions' => 4],
            $this->em->getConnection()->executeQuery(
                'SELECT permissions FROM access_rights WHERE access_rights.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetchAssociative()
        );
    }

    public function testFlagBagTypeOnNullFromDatabase(): void
    {
        $this->em->getConnection()->executeStatement(
            'INSERT INTO access_rights (uuid, permissions) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'access01']
        );

        /** @var AccessRight $card */
        $access_right = $this->em->find(AccessRight::class, $uuid);

        self::assertSame([Permissions::Read], $access_right->getPermissions()->getFlags());
    }
}
