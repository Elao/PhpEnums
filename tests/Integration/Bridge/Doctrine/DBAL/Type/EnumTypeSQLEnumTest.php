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
use App\EntityMySQL\CardSQLEnum;
use App\Enum\Suit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnumTypeSQLEnumTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        if (!preg_match('/^pdo-mysql:\/\//i', $_ENV['DOCTRINE_DBAL_URL'])) {
            self::markTestSkipped('SQL Enums can be tested only with MySQL');
        }

        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->em->getConnection()->executeQuery('DROP TABLE IF EXISTS cards_sql_enum');
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    protected function tearDown(): void
    {
        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testEnumSQLType(): void
    {
        $this->em->persist(new CardSQLEnum($uuid = 'card01', Suit::Hearts));
        $this->em->flush();
        $this->em->clear();

        /** @var Card $card */
        $card = $this->em->find(CardSQLEnum::class, $uuid);

        self::assertSame(Suit::Hearts, $card->getSuit());
    }

    public function testEnumSQLTypeOnNullFromPHP(): void
    {
        $card = new CardSQLEnum($uuid = 'card01', null);
        $this->em->persist($card);
        $this->em->flush();
        $this->em->clear();

        self::assertSame(
            ['suit' => 'S'],
            $this->em->getConnection()->executeQuery(
                'SELECT suit FROM cards_sql_enum WHERE cards_sql_enum.uuid = :uuid',
                ['uuid' => $uuid]
            )->fetchAssociative()
        );
    }

    public function testEnumSQLTypeOnNullFromDatabase(): void
    {
        $this->em->getConnection()->executeStatement(
            'INSERT INTO cards_sql_enum (uuid, suit) VALUES(:uuid, null)',
            ['uuid' => $uuid = 'card01']
        );

        /** @var Card $card */
        $card = $this->em->find(CardSQLEnum::class, $uuid);

        self::assertSame(Suit::Spades, $card->getSuit());
    }
}
