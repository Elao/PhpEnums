<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\ApiPlatform\Core\JsonSchema\Type;

use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\TypeFactoryInterface;
use Elao\Enum\EnumInterface;
use Symfony\Component\PropertyInfo\Type;

final class ElaoEnumType implements TypeFactoryInterface
{
    /** @var TypeFactoryInterface */
    private $decorated;

    public function __construct(TypeFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(Type $type, string $format = 'json', ?bool $readableLink = null, ?array $serializerContext = null, Schema $schema = null): array
    {
        if (!is_a($enumClass = $type->getClassName(), EnumInterface::class, true)) {
            return $this->decorated->getType($type, $format, $readableLink, $serializerContext, $schema);
        }

        $schema = [];
        $values = $enumClass::values();
        if ($type->isNullable() && !$type->isCollection()) {
            $values[] = null;
        }
        $enumSchema = [
            'type' => 'string',
            'enum' => $values,
            'example' => $values[0],
        ];

        if ($type->isCollection()) {
            $schema['type'] = 'array';
            $schema['items'] = $enumSchema;
        } else {
            $schema = $enumSchema;
        }

        if ($type->isNullable()) {
            $schema['nullable'] = true;
        }

        return $schema;
    }
}
