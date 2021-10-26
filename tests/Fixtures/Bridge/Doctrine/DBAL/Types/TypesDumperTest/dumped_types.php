<?php

namespace ELAO_ENUM_DT_DBAL\Foo\Bar {

    if (!\class_exists(BazType::class)) {
        class BazType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            public const NAME = 'baz';

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

    if (!\class_exists(BazWithDefaultType::class)) {
        class BazWithDefaultType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType
        {
            public const NAME = 'baz_with_default';

            protected function getEnumClass(): string
            {
                return \Foo\Bar\BazWithDefault::class;
            }

            public function getName(): string
            {
                return static::NAME;
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

    if (!\class_exists(XyzType::class)) {
        class XyzType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumSQLDeclarationType
        {
            public const NAME = 'xyz';

            protected function getEnumClass(): string
            {
                return \Foo\Bar\Xyz::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
        }
    }

    if (!\class_exists(XyzWithDefaultType::class)) {
        class XyzWithDefaultType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumSQLDeclarationType
        {
            public const NAME = 'xyz';

            protected function getEnumClass(): string
            {
                return \Foo\Bar\XyzWithDefault::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
            protected function onNullFromDatabase()
            {
                return \Foo\Bar\XyzWithDefault::get('foo');
            }

            protected function onNullFromPhp()
            {
                return 'foo';
            }
        }
    }

    if (!\class_exists(QuxType::class)) {
        class QuxType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractIntegerEnumType
        {
            public const NAME = 'qux';

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

    if (!\class_exists(FooJsonCollectionType::class)) {
        class FooJsonCollectionType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractJsonCollectionEnumType
        {
            public const NAME = 'foo_json';

            protected function getEnumClass(): string
            {
                return \Foo\Bar\Foo::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
        }
    }

    if (!\class_exists(FooCsvCollectionType::class)) {
        class FooCsvCollectionType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractCsvCollectionEnumType
        {
            public const NAME = 'foo_csv';

            protected function getEnumClass(): string
            {
                return \Foo\Bar\Foo::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
        }
    }

}

namespace ELAO_ENUM_DT_DBAL\Foo\Baz {

    if (!\class_exists(FooType::class)) {
        class FooType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractIntegerEnumType
        {
            public const NAME = 'foo';

            protected function getEnumClass(): string
            {
                return \Foo\Baz\Foo::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
        }
    }

    if (!\class_exists(FooWithDefaultType::class)) {
        class FooWithDefaultType extends \Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractIntegerEnumType
        {
            public const NAME = 'foo_with_default';

            protected function getEnumClass(): string
            {
                return \Foo\Baz\FooWithDefault::class;
            }

            public function getName(): string
            {
                return static::NAME;
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
