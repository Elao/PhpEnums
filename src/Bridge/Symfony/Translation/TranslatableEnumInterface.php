<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Translation;

use Elao\Enum\ReadableEnumInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

interface TranslatableEnumInterface extends ReadableEnumInterface, TranslatableInterface
{
}
