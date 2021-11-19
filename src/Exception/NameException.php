<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Exception;

/**
 * Exception used when providing an invalid case name for a given enum.
 */
class NameException extends InvalidArgumentException
{
    public function __construct(string $name, string $enumType)
    {
        $message = sprintf('"%s" is not an acceptable case name for "%s" enum.', $name, $enumType);

        parent::__construct($message);
    }
}
