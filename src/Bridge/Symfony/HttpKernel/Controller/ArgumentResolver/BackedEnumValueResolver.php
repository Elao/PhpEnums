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

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveBackedEnumValue;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveFrom;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class BackedEnumValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (!is_a($argument->getType(), \BackedEnum::class, true)) {
            return false;
        }

        if (!$argument->isNullable() && \in_array(null, $resolvedValues = $this->resolveValues($request, $argument), true)) {
            // do not support if the argument isn't nullable but a null value was found,
            // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error
            return false;
        }

        $resolvedValues ??= $this->resolveValues($request, $argument);

        if ([] === $resolvedValues) {
            // do not support if no value was resolved at all.
            // letting the \Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver be used
            // or \Symfony\Component\HttpKernel\Controller\ArgumentResolver fail with a meaningful error.
            return false;
        }

        return true;
    }

    private function resolveValues(Request $request, ArgumentMetadata $argument): array
    {
        $resolveConfig = $argument->getAttributes(ResolveBackedEnumValue::class, ArgumentMetadata::IS_INSTANCEOF);
        $resolveConfig = $resolveConfig[0] ?? new ResolveBackedEnumValue();

        $key = $argument->getName();
        $values = null;
        $resolved = false;

        foreach ($resolveConfig->from as $from) {
            $bag = match ($from) {
                ResolveFrom::Attributes => $request->attributes,
                ResolveFrom::Query => $request->query,
                ResolveFrom::Body => $request->request,
            };

            if ($bag->has($key)) {
                $resolved = true;
                $values = $argument->isVariadic() ? $bag->all($key) : $bag->get($key);

                break;
            }
        }

        if (!$resolved) {
            return [];
        }

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

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var class-string<\BackedEnum> $enumType */
        $enumType = $argument->getType();
        $values = $this->resolveValues($request, $argument);

        foreach ($values as $value) {
            // yield null, since we know the argument is nullable from the support method
            if ($value === null) {
                yield null;

                continue;
            }

            try {
                yield $enumType::from($value);

                continue;
            } catch (\ValueError $valueError) {
                throw new BadRequestException(sprintf(
                    'Enum type "%s" does not accept value %s',
                    $enumType,
                    json_encode($value)
                ), $valueError->getCode(), $valueError);
            }
        }
    }
}
