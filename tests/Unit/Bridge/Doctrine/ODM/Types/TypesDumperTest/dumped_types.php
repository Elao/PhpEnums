<?php

namespace ELAO_ENUM_DT_ODM\Foo\Bar {

    if (!\class_exists(BazType::class)) {
        class BazType extends \Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Bar\Baz::class;
            }
        }
    }

    if (!\class_exists(QuxType::class)) {
        class QuxType extends \Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Bar\Qux::class;
            }
        }
    }

}

namespace ELAO_ENUM_DT_ODM\Foo\Baz {

    if (!\class_exists(FooType::class)) {
        class FooType extends \Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Baz\Foo::class;
            }
        }
    }

    if (!\class_exists(FooCollectionType::class)) {
        class FooCollectionType extends \Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractCollectionEnumType
        {
            protected function getEnumClass(): string
            {
                return \Foo\Baz\Foo::class;
            }
        }
    }

}
