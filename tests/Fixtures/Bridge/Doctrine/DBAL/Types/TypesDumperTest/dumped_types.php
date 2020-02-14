<?php

namespace ELAO_ENUM_DT\Foo\Bar;

if (!\class_exists('\ELAO_ENUM_DT\Foo\Bar\BazType')) {
    class BazType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
    {
        const NAME = 'baz';

        protected function getEnumClass(): string
        {
            return \Foo\Bar\Baz::class;
        }

        public function getName(): string
        {
            return static::NAME;
        }
    }
}

namespace ELAO_ENUM_DT\Foo\Bar;

if (!\class_exists('\ELAO_ENUM_DT\Foo\Bar\QuxType')) {
    class QuxType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractIntegerEnumType
    {
        const NAME = 'qux';

        protected function getEnumClass(): string
        {
            return \Foo\Bar\Qux::class;
        }

        public function getName(): string
        {
            return static::NAME;
        }
    }
}
