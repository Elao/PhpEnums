<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Symfony\Validator\Constraint;

use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Symfony\Component\Validator\Constraints as Assert;

class ObjectWithEnumChoiceAsPhpAttribute
{
    #[Enum(class: SimpleEnum::class)]
    #[Assert\NotNull]
    public SimpleEnum $simple;

    #[Enum(class: SimpleEnum::class, choices: [SimpleEnum::ZERO, SimpleEnum::FIRST])]
    #[Assert\NotNull]
    public SimpleEnum $restrictedChoices;
}
