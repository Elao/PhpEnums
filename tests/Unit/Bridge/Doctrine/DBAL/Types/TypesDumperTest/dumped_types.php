<?php

namespace ELAO_ENUM_DT_DBAL\Foo\Bar {

    if (!\class_exists(BazType::class)) {
        class BazType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Bar\Baz::class;
            }
        }
    }

    if (!\class_exists(BazWithDefaultType::class)) {
        class BazWithDefaultType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Bar\BazWithDefault::class;
            }

            public function getName(): string
            {
                return 'baz_with_default';
            }

            protected function onNullFromDatabase()
            {
                return \Foo\Bar\BazWithDefault::get('foo');
            }

            protected function onNullFromPhp()
            {
                return 'foo';
            }
        }
    }

    if (!\class_exists(QuxType::class)) {
        class QuxType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Bar\Qux::class;
            }
        }
    }

}

namespace ELAO_ENUM_DT_DBAL\Foo\Baz {

    if (!\class_exists(FooType::class)) {
        class FooType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Baz\Foo::class;
            }

            public function getName(): string
            {
                return 'foo';
            }
        }
    }

    if (!\class_exists(FooWithDefaultType::class)) {
        class FooWithDefaultType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Baz\FooWithDefault::class;
            }

            public function getName(): string
            {
                return 'foo_with_default';
            }

            protected function onNullFromDatabase()
            {
                return \Foo\Baz\FooWithDefault::get(3);
            }

            protected function onNullFromPhp()
            {
                return 3;
            }
        }
    }

}
