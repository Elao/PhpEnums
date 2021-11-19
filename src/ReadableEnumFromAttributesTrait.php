<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Exception\LogicException;

trait ReadableEnumFromAttributesTrait
{
    use ReadableEnumTrait;

    public static function readables(): array
    {
        static $readables;

        if (!isset($readables)) {
            $r = new \ReflectionEnum(static::class);

            foreach ($r->getCases() as $case) {
                if (null === $rAttr = $case->getAttributes(EnumCase::class)[0] ?? null) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a "%s" attribute on every cases. Case "%s" is missing one.',
                        static::class,
                        ReadableEnumFromAttributesTrait::class,
                        EnumCase::class,
                        $case->name
                    ));
                }

                /** @var EnumCase $attr */
                $attr = $rAttr->newInstance();

                if (null === $attr->label) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a label using the "%s" attribute on every cases. Case "%s" is missing a label.',
                        static::class,
                        ReadableEnumFromAttributesTrait::class,
                        EnumCase::class,
                        $case->name
                    ));
                }

                $readables[$case->name] = $attr->label;
            }
        }

        return $readables;
    }
}
