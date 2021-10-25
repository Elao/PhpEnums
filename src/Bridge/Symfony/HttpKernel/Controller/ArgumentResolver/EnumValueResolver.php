<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver;

use Elao\Enum\EnumInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @final
 */
class EnumValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), EnumInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var EnumInterface $enumClass */
        $enumClass = $argument->getType();
        $requestValue = $request->get($argument->getName());

        if (!\is_array($requestValue) || !$argument->isVariadic()) {
            $requestValue = [$requestValue];
        }

        foreach ($requestValue as $value) {
            if ($enumClass::accepts($value)) {
                yield $enumClass::get($value);

                continue;
            }

            if ($value !== null || !$argument->isNullable()) {
                throw new BadRequestHttpException(sprintf('Enum "%s" does not accept value %s', $enumClass, var_export($value, true)));
            }

            yield null;
        }
    }
}
