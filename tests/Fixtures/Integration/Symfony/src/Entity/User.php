<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Elao\Enum\Tests\Fixtures\Enum\Gender;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uuid;

    /**
     * @var Gender
     *
     * @ORM\Column(type="gender", nullable=true)
     */
    private $gender;

    public function __construct(string $uuid, Gender $gender = null)
    {
        $this->gender = $gender;
        $this->uuid = $uuid;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }
}
