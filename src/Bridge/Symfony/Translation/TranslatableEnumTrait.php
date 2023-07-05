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

use Elao\Enum\ReadableEnumTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableEnumTrait
{
    use ReadableEnumTrait;

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans($this->getReadable(), [], $locale);
    }
}
