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
        $user = new User(Gender::MALE());
        $this->dm->persist($user);
        $this->dm->flush();
        $this->dm->clear();

        $user = $this->dm->find(User::class, $user->getId());

        self::assertSame(Gender::MALE(), $user->getGender());
    }

    public function testEnumTypeOnNullFromPHP(): void
    {
        $user = new User();
        $this->dm->persist($user);
        $this->dm->flush();
        $this->dm->clear();

        /** @var User $user */
        $user = $this->dm->find(User::class, $user->getId());

        self::assertNull($user->getGender());
    }

    public function testQueryByValueOrEnumInstance(): void
    {
        $user = new User(Gender::FEMALE());
        $this->dm->persist($user);
        $this->dm->flush();
        $this->dm->clear();

        $qb = $this->dm->createQueryBuilder(User::class);
        $repo = $this->dm->getRepository(User::class);

        self::assertEquals($user, $qb->field('gender')->equals(Gender::FEMALE())->getQuery()->toArray()[0]);
        self::assertEquals($user, $qb->field('gender')->equals(Gender::FEMALE)->getQuery()->toArray()[0]);
        self::assertEmpty($qb->field('gender')->equals(Gender::MALE())->getQuery()->toArray());

        self::assertEquals($user, $repo->findOneBy(['gender' => Gender::FEMALE()]));
        self::assertEquals($user, $repo->findOneBy(['gender' => Gender::FEMALE]));
        self::assertNull($repo->findOneBy(['gender' => Gender::MALE()]));
    }

    public function testEnumIsConvertedToValueDuringQuery(): void
    {
        $qb = $this->dm->createQueryBuilder(User::class);

        self::assertSame('female', $qb->field('gender')->equals(Gender::FEMALE())->getQuery()->debug('query')['gender']);
        self::assertSame('male', $qb->field('gender')->equals(Gender::MALE)->getQuery()->debug('query')['gender']);
        self::assertNull($qb->field('gender')->equals(null)->getQuery()->debug('query')['gender']);
    }

    public function testEnumTypeOnNullFromDatabase(): void
    {
        $insert = $this->dm->getDocumentCollection(User::class)->insertOne(['_id' => new ObjectId(), 'gender' => null]);
        $user = $this->dm->find(User::class, $insert->getInsertedId());

        self::assertNull($user->getGender());
    }
}
