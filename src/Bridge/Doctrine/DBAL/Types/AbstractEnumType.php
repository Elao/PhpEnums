<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;

if (enum_exists(ParameterType::class)) {
    abstract class AbstractEnumType extends AbstractEnumTypeCommon
    {
        /**
         * {@inheritdoc}
         */
        public function getBindingType(): ParameterType
        {
            return $this->isIntBackedEnum() ? ParameterType::INTEGER : ParameterType::STRING;
        }
    }
} else {
    abstract class AbstractEnumType extends AbstractEnumTypeCommon
    {
        /**
         * {@inheritdoc}
         */
        public function getBindingType(): int
        {
            return $this->isIntBackedEnum() ? ParameterType::INTEGER : ParameterType::STRING;
        }
    }
}
