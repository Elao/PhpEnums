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
 * Exception that represents error in the program logic or base classes misuses.
 * This kind of exceptions should directly lead to a fix in your code.
 */
class LogicException extends \LogicException implements ExceptionInterface
{
}
