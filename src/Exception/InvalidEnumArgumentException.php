<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Exception;

class InvalidEnumArgumentException extends \InvalidArgumentException
{
    public function __construct($value, $class)
    {
        $message = sprintf('%s is not an acceptable value for "%s" enum.', json_encode($value), $class);

        parent::__construct($message);
    }
}
