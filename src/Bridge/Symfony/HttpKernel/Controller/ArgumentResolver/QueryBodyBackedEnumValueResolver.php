<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver;

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromBody;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @internal
 */
function resolveValues(Request $request, ArgumentMetadata $argument): array
{
    $from = $argument->getAttributes(BackedEnumFromQuery::class, ArgumentMetadata::IS_INSTANCEOF)[0]
        ?? $argument->getAttributes(BackedEnumFromBody::class, ArgumentMetadata::IS_INSTANCEOF)[0]
        ?? null;

    if (null === $from) {
        return [];
    }

    $key = $argument->getName();

    $bag = match (true) {
        $from instanceof BackedEnumFromQuery => $request->query,
        $from instanceof BackedEnumFromBody => $request->request,
        default => throw new \LogicException(sprintf('Unexpected attribute class "%s"', get_debug_type($from))),
    };

    if (!$bag->has($key)) {
        return [];
    }

    $values = $argument->isVariadic() ? $bag->all($key) : $bag->get($key);

    if (!$argument->isVariadic()) {
        $values = [$values];
    }

    foreach ($values as &$value) {
        // Consider empty string from query/body as null
        if ($value === '') {
            $value = null;
        }
    }

    return $values;
}

// Legacy (<6.2) resolver
if (!interface_exists(ValueResolverInterface::class)) {
    /**
     * @final
     */
    class QueryBodyBackedEnumValueResolver implements ArgumentValueResolverInterface
    {
        public function supports(Request $request, ArgumentMetadata $argument): bool
        {
            if (!is_a($argument->getType(), \BackedEnum::class, true)) {
                return false;
            }

            $resolvedValues = resolveValues($request, $argument);

            if ([] === $resolvedValues) {
                // do not support if no value was resolved at all.
                // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver be used
                // or \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error.
                return false;
            }

            if (!$argument->isNullable() && \in_array(null, $resolvedValues, true)) {
                // do not support if the argument isn't nullable but a null value was found,
                // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error
                return false;
            }

            return true;
        }

        public function resolve(Request $request, ArgumentMetadata $argument): iterable
        {
            $values = resolveValues($request, $argument);

            foreach ($values as $value) {
                if ($value === null) {
                    yield null;

                    continue;
                }

                /** @var class-string<\BackedEnum> $enumType */
                $enumType = $argument->getType();

                try {
                    yield $enumType::from($value);
                } catch (\ValueError|\TypeError $error) {
                    throw new BadRequestException(sprintf(
                        'Could not resolve the "%s $%s" controller argument: %s',
                        $argument->getType(),
                        $argument->getName(),
                        $error->getMessage(),
                    ));
                }
            }
        }
    }

    return;
}

/**
 * @final
 */
class QueryBodyBackedEnumValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType(), \BackedEnum::class, true)) {
            return [];
        }

        $resolvedValues = resolveValues($request, $argument);

        if ([] === $resolvedValues) {
            // do not support if no value was resolved at all.
            // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver be used
            // or \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error.
            return [];
        }

        if (!$argument->isNullable() && \in_array(null, $resolvedValues, true)) {
            // do not support if the argument isn't nullable but a null value was found,
            // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error
            return [];
        }

        foreach ($resolvedValues as $value) {
            if ($value === null) {
                yield null;

                continue;
            }

            /** @var class-string<\BackedEnum> $enumType */
            $enumType = $argument->getType();

            try {
                yield $enumType::from($value);
            } catch (\ValueError|\TypeError $error) {
                throw new BadRequestException(sprintf(
                    'Could not resolve the "%s $%s" controller argument: %s',
                    $argument->getType(),
                    $argument->getName(),
                    $error->getMessage(),
                ));
            }
        }
    }
}
