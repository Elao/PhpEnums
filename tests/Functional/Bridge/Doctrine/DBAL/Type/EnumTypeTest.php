<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Functional\Bridge\Doctrine\DBAL\Type;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Elao\Enum\Tests\Fixtures\Functional\Symfony\TestBundle\Entity\User;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $em;

    protected function setUp()
    {
        static::bootKernel();
        $kernel = static::$kernel;
        $container = $kernel->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        (new ORMPurger($this->em))->purge();
    }

    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testEnumType()
    {
        $this->em->persist(new User($uuid = 'user01', Gender::create(Gender::MALE)));
        $this->em->flush();
        $this->em->clear();

        $user = $this->em->find(User::class, $uuid);

        $this->assertInstanceOf(Gender::class, $user->getGender());
        $this->assertTrue($user->getGender()->is(Gender::MALE));
    }

    public function testEnumTypeOnNullFromPHP()
    {
        $this->em->persist(new User($uuid = 'user01', null));
        $this->em->flush();
        $this->em->clear();

        $this->assertSame(
            ['gender' => 'unknown'],
            $this->em->getConnection()->executeQuery(
                'SELECT gender FROM user WHERE user.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetch()
        );
    }

    public function testEnumTypeOnNullFromDatabase()
    {
        $this->em->getConnection()->executeUpdate(
            'INSERT INTO user (uuid, gender) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'user01']
        );

        $user = $this->em->find(User::class, $uuid);

        $this->assertInstanceOf(Gender::class, $user->getGender());
        $this->assertTrue($user->getGender()->is(Gender::UNKNOW));
    }
}
