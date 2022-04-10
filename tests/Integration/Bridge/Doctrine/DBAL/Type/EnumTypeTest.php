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

use App\Entity\Card;
use App\Entity\CardSQLEnum;
use App\Enum\Suit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeTest extends KernelTestCase
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

    public function testEnumType(): void
    {
        $this->em->persist(new Card($uuid = 'card01', Suit::Hearts));
        $this->em->flush();
        $this->em->clear();

        /** @var Card $card */
        $card = $this->em->find(Card::class, $uuid);

        self::assertSame(Suit::Hearts, $card->getSuit());
    }

    public function testEnumSQLType(): void
    {
        self::markTestSkipped('SQL Enum tests skipped for now');

        $this->em->persist(new CardSQLEnum($uuid = 'card01', Suit::Hearts));
        $this->em->flush();
        $this->em->clear();

        /** @var Card $card */
        $card = $this->em->find(CardSQLEnum::class, $uuid);

        self::assertSame(Suit::Hearts, $card->getSuit());
    }

    public function testEnumTypeOnNullFromPHP(): void
    {
        $this->em->persist(new Card($uuid = 'card01', null));
        $this->em->flush();
        $this->em->clear();

        self::assertSame(
            ['suit' => 'S'],
            $this->em->getConnection()->executeQuery(
                'SELECT suit FROM cards WHERE cards.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetch()
        );
    }

    public function testEnumSQLTypeOnNullFromPHP(): void
    {
        self::markTestSkipped('SQL Enum tests skipped for now');

        $card = new CardSQLEnum($uuid = 'card01', null);
        $this->em->persist($card);
        $this->em->flush();
        $this->em->clear();

        self::assertSame(
            ['suit' => 'S'],
            $this->em->getConnection()->executeQuery(
                'SELECT suit FROM cards_sql_enum WHERE cards_sql_enum.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetch()
        );
    }

    public function testEnumTypeOnNullFromDatabase(): void
    {
        $this->em->getConnection()->executeUpdate(
            'INSERT INTO cards (uuid, suit) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'card01']
        );

        /** @var Card $card */
        $card = $this->em->find(Card::class, $uuid);

        self::assertSame(Suit::Spades, $card->getSuit());
    }

    public function testEnumSQLTypeOnNullFromDatabase(): void
    {
        self::markTestSkipped('SQL Enum tests skipped for now');

        $this->em->getConnection()->executeUpdate(
            'INSERT INTO cards_sql_enum (uuid, suit) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'card01']
        );

        /** @var Card $card */
        $card = $this->em->find(CardSQLEnum::class, $uuid);

        self::assertSame(Suit::Spades, $card->getSuit());
    }
}
