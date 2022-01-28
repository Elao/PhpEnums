<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Document;

use App\Enum\Suit;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Card
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: Suit::class, nullable: true)]
    private ?Suit $suit;

    public function __construct(?Suit $suit = null)
    {
        $this->suit = $suit;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSuit(): ?Suit
    {
        return $this->suit;
    }
}
