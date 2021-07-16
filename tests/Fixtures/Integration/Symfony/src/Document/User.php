<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Elao\Enum\Tests\Fixtures\Enum\Gender;

/**
 * @ODM\Document()
 */
class User
{
    /**
     * @ODM\Id()
     */
    private $id;

    /**
     * @var Gender
     *
     * @ODM\Field(type="gender")
     */
    private $gender;

    public function __construct(?Gender $gender = null)
    {
        $this->gender = $gender;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
