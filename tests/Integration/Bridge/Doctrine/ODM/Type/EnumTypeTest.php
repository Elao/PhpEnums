<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Doctrine\ODM\Type;

use App\Document\Card;
use App\Enum\Suit;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\SkippedTestSuiteError;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeTest extends KernelTestCase
{
    private DocumentManager $dm;

    public static function setUpBeforeClass(): void
    {
        if (!class_exists(DoctrineMongoDBBundle::class)) {
            throw new SkippedTestSuiteError('Doctrine MongoDB ODM bundle not installed');
        }
    }

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->dm = $container->get('doctrine_mongodb.odm.document_manager');

        foreach ($this->dm->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata->isMappedSuperclass) {
                continue;
            }

            $this->dm->getDocumentCollection($metadata->name)->drop();
        }

        $this->dm->getSchemaManager()->ensureIndexes();
    }

    protected function tearDown(): void
    {
        $this->dm->close();

        parent::tearDown();
    }

    public function testEnumType(): void
    {
        $card = new Card(Suit::Hearts);
        $this->dm->persist($card);
        $this->dm->flush();
        $this->dm->clear();

        $card = $this->dm->find(Card::class, $card->getId());

        self::assertSame(Suit::Hearts, $card->getSuit());
    }

    public function testEnumTypeOnNullFromPHP(): void
    {
        $card = new Card();
        $this->dm->persist($card);
        $this->dm->flush();
        $this->dm->clear();

        $card = $this->dm->find(Card::class, $card->getId());

        self::assertNull($card->getSuit());
    }

    public function testQueryByValueOrEnumInstance(): void
    {
        $card = new Card(Suit::Clubs);
        $this->dm->persist($card);
        $this->dm->flush();
        $this->dm->clear();

        $qb = $this->dm->createQueryBuilder(Card::class);
        $repo = $this->dm->getRepository(Card::class);

        self::assertEquals($card, $qb->field('suit')->equals(Suit::Clubs)->getQuery()->toArray()[0]);
        self::assertEquals($card, $qb->field('suit')->equals('C')->getQuery()->toArray()[0]);
        self::assertEmpty($qb->field('suit')->equals(Suit::Hearts)->getQuery()->toArray());
        self::assertEmpty($qb->field('suit')->equals('H')->getQuery()->toArray());

        self::assertEquals($card, $repo->findOneBy(['suit' => Suit::Clubs]));
        self::assertEquals($card, $repo->findOneBy(['suit' => 'C']));
        self::assertNull($repo->findOneBy(['suit' => Suit::Hearts]));
    }

    public function testEnumIsConvertedToValueDuringQuery(): void
    {
        $qb = $this->dm->createQueryBuilder(Card::class);

        self::assertSame('C', $qb->field('suit')->equals(Suit::Clubs)->getQuery()->debug('query')['suit']);
        self::assertSame('H', $qb->field('suit')->equals('H')->getQuery()->debug('query')['suit']);
        self::assertNull($qb->field('suit')->equals(null)->getQuery()->debug('query')['suit']);
    }

    public function testEnumTypeOnNullFromDatabase(): void
    {
        $insert = $this->dm->getDocumentCollection(Card::class)->insertOne(['_id' => new ObjectId(), 'suit' => null]);
        $card = $this->dm->find(Card::class, $insert->getInsertedId());

        self::assertNull($card->getSuit());
    }
}
