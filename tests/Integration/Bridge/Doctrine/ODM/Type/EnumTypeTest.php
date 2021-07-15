<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Doctrine\ODM\Type;

use App\Document\User;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\ODM\MongoDB\DocumentManager;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use MongoDB\BSON\ObjectId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeTest extends KernelTestCase
{
    /** @var DocumentManager */
    private $dm;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->dm = $container->get('doctrine_mongodb.odm.document_manager');
        (new MongoDBPurger($this->dm))->purge();
    }

    public function testEnumType(): void
    {
        $user = new User(Gender::get(Gender::MALE));
        $this->dm->persist($user);
        $this->dm->flush();
        $this->dm->clear();

        $user = $this->dm->find(User::class, $user->getId());

        self::assertTrue($user->getGender()->is(Gender::MALE));
    }

    public function testEnumTypeOnNullFromPHP(): void
    {
        $this->markTestIncomplete('Null behavior is not working on Mongo');
        $user = new User();
        $this->dm->persist($user);
        $this->dm->flush();
        $this->dm->clear();

        /** @var User $user */
        $user = $this->dm->find(User::class, $user->getId());

        self::assertSame(Gender::UNKNOW(), $user->getGender());
    }

    public function testEnumTypeOnNullFromDatabase(): void
    {
        $this->markTestIncomplete('Null behavior is not working on Mongo');
        $insert = $this->dm->getDocumentCollection(User::class)->insertOne(['_id' => new ObjectId(), 'gender' => null]);
        $user = $this->dm->find(User::class, $insert->getInsertedId());

        self::assertSame(Gender::UNKNOW(), $user->getGender());
    }
}
