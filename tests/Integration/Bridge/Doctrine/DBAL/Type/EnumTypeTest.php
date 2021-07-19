<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Doctrine\DBAL\Type;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $em;

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

    public function testEnumType(): void
    {
        $this->em->persist(new User($uuid = 'user01', Gender::get(Gender::MALE)));
        $this->em->flush();
        $this->em->clear();

        $user = $this->em->find(User::class, $uuid);

        self::assertTrue($user->getGender()->is(Gender::MALE));
    }

    public function testEnumTypeOnNullFromPHP(): void
    {
        $this->em->persist(new User($uuid = 'user01', null));
        $this->em->flush();
        $this->em->clear();

        self::assertSame(
            ['gender' => 'unknown'],
            $this->em->getConnection()->executeQuery(
                'SELECT gender FROM user WHERE user.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetch()
        );
    }

    public function testEnumTypeOnNullFromDatabase(): void
    {
        $this->em->getConnection()->executeUpdate(
            'INSERT INTO user (uuid, gender) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'user01']
        );

        $user = $this->em->find(User::class, $uuid);

        self::assertTrue($user->getGender()->is(Gender::UNKNOW));
    }
}
