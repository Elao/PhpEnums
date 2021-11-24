<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

/**
 * An opinionated enum implementation:
 *
 * - auto-discovers enumerated values from public constants.
 * - implements {@link \Elao\Enum\ReadableEnumInterface} with default labels
 *   identical to enumerated values's constant name.
 *
 * @template T of int|string
 * @extends ReadableEnum<T>
 */
abstract class SimpleChoiceEnum extends ReadableEnum
{
    /** @use AutoDiscoveredValuesTrait<T> */
    use AutoDiscoveredValuesTrait;
    /** @use ChoiceEnumTrait<T> */
    use ChoiceEnumTrait {
        ChoiceEnumTrait::values insteadof AutoDiscoveredValuesTrait;
    }
}
