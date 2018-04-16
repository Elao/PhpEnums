<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Exception;

/**
 * Exception used when providing an invalid value for a given enumeration class.
 */
class InvalidValueException extends InvalidArgumentException
{
    public function __construct($value, $class)
    {
        $message = sprintf('%s is not an acceptable value for "%s" enum.', json_encode($value), $class);

        parent::__construct($message);
    }
}
