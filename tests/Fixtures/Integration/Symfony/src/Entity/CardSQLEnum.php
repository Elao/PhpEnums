<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Entity;

use App\Enum\Suit;
use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity]
// #[ORM\Table(name: 'cards_sql_enum')]
class CardSQLEnum
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $uuid;

    #[ORM\Column(type: 'suit_sql_enum', nullable: true)]
    private ?Suit $suit;

    public function __construct(string $uuid, ?Suit $suit = null)
    {
        $this->uuid = $uuid;
        $this->suit = $suit;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSuit(): ?Suit
    {
        return $this->suit;
    }
}
