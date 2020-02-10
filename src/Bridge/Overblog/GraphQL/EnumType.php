<?php declare(strict_types=1);

namespace Elao\Enum\Bridge\Overblog\GraphQL;

use Elao\Enum\EnumInterface;
use GraphQL\Type\Definition;

abstract class EnumType extends Definition\EnumType
{
    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct([
            'values' => $this->createValues(),
            'description' => $this->description,
        ] + $config);
    }

    /**
     * Override to customize how enum values are created.
     *
     * @return array<string, array<string>>
     */
    protected function createValues(): array
    {
        $values = [];

        foreach (static::getEnumClass()::values() as $value) {
            $values[$this->getEnumValueName($value)] = ['value' => $value];
        }

        return $values;
    }

    /**
     * @param int|string $value One of the EnumInterface implementation enumerated value
     *
     * @return string|int
     */
    protected function getEnumValueName($value)
    {
        return $value;
    }

    /**
     * @return string The enum FQCN for which we should create a type.
     */
    abstract protected static function getEnumClass(): string;

    /**
     * {@inheritdoc}
     *
     * @param EnumInterface|null $value
     */
    public function serialize($value)
    {
        return $value ? $value->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function parseValue($value)
    {
        if ($value === null) {
            return null;
        }

        return static::getEnumClass()::get(parent::parseValue($value) ?? $value);
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed>|null $variables
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode === null) {
            return null;
        }

        return static::getEnumClass()::get(parent::parseLiteral($valueNode) ?? $valueNode);
    }
}
