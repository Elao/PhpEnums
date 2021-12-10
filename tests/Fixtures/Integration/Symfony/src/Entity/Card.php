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

use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Tests\Fixtures\Enum\Suit;

#[ORM\Entity]
#[ORM\Table(name: 'cards')]
class Card
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $uuid;

    #[ORM\Column(type: Suit::class, nullable: true)]
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

    public function getSuit(): Suit
    {
        return $this->suit;
    }
}
