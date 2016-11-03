Elao Enumerations
=================

This project is greatly inspired by the [BiplaneEnumBundle](https://github.com/yethee/BiplaneEnumBundle) and aims to provide the missing PHP enumerations support.

It'll power main frameworks integrations and bridges with other libraries when relevant. 

# Features

- Base implementation for simple, readable and flagged (bitmask) enumerations based on the [BiplaneEnumBundle](https://github.com/yethee/BiplaneEnumBundle) ones.
- Symfony's Form component integration with form types.
- Symfony's Serializer component integration with a normalizer class.
- Doctrine DBAL integration with abstract classes aiming to easy storing your enumeration in the database.

# Installation

```sh
$ composer require elao/enum
```

## With Symfony full stack framework

Nothing to do. We won't register anything in the Symfony DIC for now.  
Simply use provided classes.

However, you can register Symfony's Serializer normalizers yourself:

```yml
# services.yml

services:
    app.enum_normalizer:
        class: 'Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer'
        tags: [{ name: serializer.normalizer }]
```

# Usage

Declare your own enumeration by creating a class extending `Elao\Enum\Enum`:

```php
<?php

use Elao\Enum\Enum;

class Gender extends Enum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getPossibleValues(): array
    {
        return [
            self::UNKNOW, 
            self::MALE, 
            self::FEMALE
        ];
    }
}
```

Create a new instance of your enumeration:

```php
<?php

$enum = Gender::create(Gender::Male); 
```

The main advantage of this approach is to manipulate your enums as objects, which can be convenient in many situations in your domain, and allow to typehint methods.

You can easily retrieve the enumeration's value by using `$enum->getValue();`

API:

- `public static function create($value): EnumInterface`: Creates a new instance of an enumeration
- `public static function getPossibleValues(): array`: Should return any possible value for the enumeration
- `public static function isAcceptableValue($value): bool`: True if the value is acceptable for this enumeration
- `public function getValue()`: Returns the enumeration instance value
- `public function equals(EnumInterface $enum): bool`: Determines whether two enumerations instances should be considered the same
- `public function is($value): bool`: Determines if the enumeration instance value is equal to the given value.
- `public static function getPossibleInstances(): array`: Instantiate and returns an array containing every enumeration instances for possible values.

## Readable enums

Sometimes, enums may be displayed to the user, or exported in a human readable way.  
Hence comes the `ReadableEnum`:

```php
<?php

use Elao\Enum\ReadableEnum;

class Gender extends ReadableEnum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getPossibleValues(): array
    {
        return [
            self::UNKNOW,
            self::MALE,
            self::FEMALE,
        ];
    }

    public static function getReadables(): array
    {
        return [
            self::UNKNOW => 'Unknown',
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        ];
    }
}
```

The following snippet shows how to render the human readable value of an enum:

```php
<?php
$enum = Gender::create(Gender::Male); 
$enum->getReadable(); // returns 'Male'
(string) $enum->getReadable(); // returns 'Male'
```

If you're using a translation library, you can also simply return translation keys from the `ReadableEnumInterface::getReadables()` method:

```php
<?php

use Elao\Enum\ReadableEnum;

class Gender extends ReadableEnum
{
    // ...
    
    public static function getReadables(): array
    {
        return [
            self::UNKNOW => 'enum.gender.unknown',
            self::MALE => 'enum.gender.male',
            self::FEMALE => 'enum.gender.female',
        ];
    }
}
```

Using Symfony's translation component:

 
```yaml
 # app/Resources/translations/messages.en.yml
 enum.gender.unknown: 'Unknown'
 enum.gender.male: 'Male'
 enum.gender.female: 'Female'
```

```php
<?php
$enum = Gender::create(Gender::MALE);
// get translator instance...
$translator->trans($enum); // returns 'Male'
```

API:

- `public static function getReadables(): array`: Should return an array of the human representations indexed by possible values.
- `public static function getReadableFor($value): string`: Get the human representation for given enumeration value.
- `public function getReadable(): string`: Get the human representation for the current instance.

## Flagged enums

Flagged enumerations are used for bitwise operations.
Each value of the enumeration is a single bit flag and can be combined together into a valid bitmask in a single enum instance.

```php
<?php

use Elao\Enum\FlaggedEnum;

class Permissions extends FlaggedEnum
{
    const EXECUTE = 1;
    const WRITE = 2;
    const READ = 4;

    // You can declare shortcuts for common bit flags combinations
    // but it should not appear in `getPossibleValues`
    const ALL = self::EXECUTE | self::WRITE | self::READ;

    public static function getPossibleValues(): array
    {
        return [
            // Only declare valid bit flags:
            static::EXECUTE,
            static::WRITE,
            static::READ,
        ];
    }

    public static function getReadables(): array
    {
        return [
            static::EXECUTE => 'Execute',
            static::WRITE => 'Write',
            static::READ => 'Read',

            // You can define readable values for specific bit flags combinations:
            static::WRITE | static::READ => 'Read & write',
            static::EXECUTE | static::READ => 'Read & execute',
            static::ALL => 'All permissions',
        ];
    }
}
```

Create instances using bitwise operations and manipulate them:

```php
<?php
$permissions = Permissions::create(Permissions::EXECUTE | Permissions::WRITE | Permissions::READ);
$permissions = $permissions->removeFlags(Permissions::EXECUTE); // Returns a new instance without "execute" flag
$permissions->getValue(); // Returns 6 (int)
$permissions->getFlags(); // Returns [2, 4] (=> [Permissions::EXECUTE, Permissions::WRITE]

$permissions = $permissions->removeFlags(Permissions::READ | Permissions::WRITE); // Returns a new instance without "read" and "write" flags
$permissions->getValue(); // Returns Permissions::NONE (0)
$permissions->getFlags(); // Returns an empty array

$permissions = Permissions::create(Permissions::NONE); // Creates an empty bitmask instance
$permissions->addFlags(Permissions::READ | Permissions::EXECUTE); // Returns a new instance with "read" and "execute" permissions
$permissions->hasFlag(Permissions::READ); // True
$permissions->hasFlag(Permissions::READ | Permissions::EXECUTE); // True
$permissions->hasFlag(Permissions::WRITE); // False
```

API:

- `public static function isAcceptableValue($value): bool`: Same as before, but accepts bit flags and bitmasks.
- `public static function getReadableFor($value, string $separator = '; '): string`: Same as before, but allows to specify a delimiter between single bit flags (if no human readable representation is found for the combination)
- `public function getReadable(string $separator = '; '): string`: Same as before, but with a delimiter option (see above) 
- `public function getFlags(): array`: Returns an array of bit flags set in the current enumeration instance.
- `public function hasFlag(int $bitFlag): bool`: True if the current instance has the given bit flag(s).
- `public function addFlags(int $flags): self`: Returns a new instance of the enumeration with additional flag(s).
- `public function removeFlags(int $flags): self`: Returns a new instance of the enumeration without given flag(s).
- `protected static function getReadableForNone(): string`: Override to replace the default human representation of the "no flag" value.

# Persisting enums into databases (With Doctrine DBAL and ORM)

You can store the raw value of an enumeration in the database, but still manipulte it as an object in your entities by [creating a custom DBAL type from scratch](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html).

However, this library can help you by providing abstract classes for both string and integer based enumerations.

## Create the DBAL type

First, create your DBAL type by extending either `AbstractEnumType` (string based enum) or `AbstractIntegerEnumType` (integer based enum, for flagged enums for instance):

```php
<?php

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;

class GenderEnumType extends AbstractEnumType
{
    const NAME = 'gender';

    protected function getEnumClass() : string
    {
        return Gender::class;
    }

    public function getName()
    {
        return static::NAME;
    }
}
```

## Register the DBAL type

Then, you'll simply need to register your DBAL type:

### Manually

```php
<?php
// in bootstrapping code

// ...

use Doctrine\DBAL\Types\Type;
Type::addType(GenderEnumType::NAME, GenderEnumType::class);
```

To convert the underlying database type of your new "gender" type directly into an instance of `Gender` when performing schema operations, the type has to be registered with the database platform as well:

```php
<?php
$conn = $em->getConnection();
$conn->getDatabasePlatform()->registerDoctrineTypeMapping(GenderEnumType::NAME, GenderEnumType::class);
```

### Using the Doctrine Bundle with Symfony

refs: 

- [Registering custom Mapping Types](https://symfony.com/doc/current/doctrine/dbal.html#registering-custom-mapping-types)
- [Registering custom Mapping Types in the SchemaTool](https://symfony.com/doc/current/doctrine/dbal.html#registering-custom-mapping-types-in-the-schematool)

```yml
# app/config/config.yml
doctrine:
    dbal:
        types:
            gender:  AppBundle\Doctrine\DBAL\Types\GenderEnumType
        mapping_types:
            gender: string
```

## Mapping

When registering the custom types in the configuration you specify a unique name for the mapping type and map that to the corresponding fully qualified class name. Now the new type can be used when mapping columns:

```php
<?php
class User
{
    /** @Column(type="gender") */
    private $gender;
}
```

### Default value on `null`

Two methods allow to set a default value if `null` is retrieved from the database, or before persisting a value:

```php
<?php

abstract class AbstractEnumType extends Type
{
    // ...

    /**
     * What should be returned on null value from the database.
     *
     * @return mixed
     */
    protected function onNullFromDatabase()
    {
        return null;
    }

    /**
     * What should be returned on null value from PHP.
     *
     * @return mixed
     */
    protected function onNullFromPhp()
    {
        return null;
    }
}
```

Override those methods in order to satisfy your needs.

# Integration with Symfony's Form component

## Simple enums

Simply use the `EnumType`:

```php
<?php

use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use MyApp\Enum\Gender;

// ...

$builder->add('gender', EnumType::class, [
    'enum_class' => Gender::class,
]);

// ...

$form->submit($data);
$form->get('gender')->getData(); // Will return a `Gender` instance (or null)
```

Only the `enum_class` option is required.

You can used any [`ChoiceType`](https://symfony.com/doc/current/reference/forms/types/choice.html) option as usual (for instance the `multiple` option).

The field data will be an instance of your enum. If you only want to map values, you can use the `as_value` option:

```php
<?php

use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use MyApp\Enum\Gender;

// ...

$builder->add('gender', EnumType::class, [
    'enum_class' => Gender::class,
    'as_value' => true,
]);

// ...

$form->submit($data);
$form->get('gender')->getData(); // Will return a string value defined in the `Gender` enum (or null)
```

You can restrict the list of proposed enumerations by overriding the `choices` option:

```php
<?php

use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use MyApp\Enum\Gender;

// ...

$builder->add('gender', EnumType::class, [
    'enum_class' => Gender::class,
    'choices' => [
        Gender::create(Gender::MALE), 
        Gender::create(Gender::FEMALE),
    ],
]);

// or:

$builder->add('gender', EnumType::class, [
    'enum_class' => Gender::class,
    'as_value' => true,
    'choices' => [
        Gender::getReadableFor(Gender::MALE) => Gender::MALE,
        Gender::getReadableFor(Gender::FEMALE) => Gender::FEMALE,
    ],
]);
```

## Flagged enums

Simply use the `FlaggedEnumType` (which extends `EnumType`):

```php
<?php

use Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType;
use MyApp\Enum\Permissions;

// ...

$builder->add('permissions', FlaggedEnumType::class, [
    'enum_class' => Permissions::class,
]);

// ...

$form->submit($data);
$form->get('permissions')->getData(); // Will return a single `Permissions` instance composed of selected bit flags
```

Same options are available, but on the contrary of the `EnumType`, the `multiple` option is always `true` and cannot be set to `false` (You'll always get a single enum instance though).
