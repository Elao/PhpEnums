Elao Enumerations
=================
[![Latest Stable Version](https://poser.pugx.org/elao/enum/v/stable?format=flat-square)](https://packagist.org/packages/elao/enum) 
[![Total Downloads](https://poser.pugx.org/elao/enum/downloads?format=flat-square)](https://packagist.org/packages/elao/enum) 
[![Monthly Downloads](https://poser.pugx.org/elao/enum/d/monthly?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Latest Unstable Version](https://poser.pugx.org/elao/enum/v/unstable?format=flat-square)](https://packagist.org/packages/elao/enum) 
[![License](https://poser.pugx.org/elao/enum/license?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Build Status](https://img.shields.io/travis/Elao/PhpEnums/master.svg?style=flat-square)](https://travis-ci.org/Elao/PhpEnums)
[![Coveralls](https://img.shields.io/coveralls/Elao/PhpEnums.svg?style=flat-square)](https://coveralls.io/github/Elao/PhpEnums)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/Elao/PhpEnums.svg?style=flat-square)](https://scrutinizer-ci.com/g/Elao/PhpEnums/?branch=master)

This project is greatly inspired by the [BiplaneEnumBundle](https://github.com/yethee/BiplaneEnumBundle) and aims to provide the missing PHP enumerations support.

It will leverage integration of the main PHP frameworks and also provide bridges with other libraries when relevant.

Table of Contents
=================

  * [Why?](#why)
  * [Features](#features)
  * [Installation](#installation)
    * [With Symfony full stack framework](#with-symfony-full-stack-framework)
  * [Usage](#usage)
    * [Readable enums](#readable-enums)
    * [Flagged enums](#flagged-enums)
    * [Compare](#compare)
    * [Shortcuts](#shortcuts)
  * [Integrations](#integrations)
    * [Doctrine](#doctrine)
      * [Create the DBAL type](#create-the-dbal-type)
      * [Register the DBAL type](#register-the-dbal-type)
        * [Manually](#manually)
        * [Using the Doctrine Bundle with Symfony](#using-the-doctrine-bundle-with-symfony)
      * [Mapping](#mapping)
      * [Default value on null](#default-value-on-null)
    * [Symfony Serializer component](#symfony-serializer-component)
    * [Symfony Form component](#symfony-form-component)
      * [Simple enums](#simple-enums)
      * [Flagged enums](#flagged-enums-1)
  * [API](#api)
    * [Simple enum](#simple-enum)
    * [Readable enum](#readable-enum)
    * [Flagged enum](#flagged-enum)

# Why?

An enumeration is a data type, enclosing a single value from a predictable set of members (enumerators). 
Each enumerator name is a single identifier, materialized by a PHP constant.  

Using an enum class provides many benefits:

- Brings visibility in your code
- Provides Type Hinting when using the enum class
- Centralizes enumeration logic within a class
- Defines utility methods or minor logic owned by your enumeration
- Helps to describe how to read, serialize, export \[, ...\] an enumeration
- Allows common libraries and frameworks integrations.

Enumerations are not options and are not meant to replace constants. Designing an enum type should be done by keeping your domain in mind, and should convey a strong meaning on your application logic.

Wrong use-cases:

- A set of options used by a library or a method.
- An unpredictable set of elements.
- An non-reusable set of elements inside the application.
- Long sets of elements (languages, locales, currencies, ...)
- Holding variable data inside the enum type (use an intermediate value object holding the data and the enum instance instead).

Valid use-cases:

- Gender, civility, predictable roles and permissions, ...
- A set of supported nodes in an importer, or a set of predefined attributes.
- In a game: predefined actions, movement directions, character classes, weapon types, ...
- Any other set of restricted elements.

Why another library ?

- [`myclabs/php-enum`](https://github.com/myclabs/php-enum) provides a base enum implementation as a class, inspired from `\SplEnum`. However, it doesn't provide as many features nor integrations as we wish to.
- [`commerceguys/enum`](https://github.com/commerceguys/enum) only acts as a utility class, but does not intend to instantiate enumerations. Hence, it doesn't allow as many features nor integrations with third-party libraries and frameworks. Manipulating enums as objects is also one of the first motivations of this project.
- [`yethee/BiplaneEnumBundle`](https://github.com/yethee/BiplaneEnumBundle) is the first library we got inspiration from. But it was designed as a Symfony Bundle, whereas we opted for a component first approach. Integrations are then provided in a dedicated `Bridge` namespace and are not restricted to Symfony.

Finally, we used to create similar classes from scratch in some projects lately.  
Providing our own package inspired from the best ones, on which we'll apply our own philosophy looks a better way to go.

# Features

- Base implementation for simple, readable and flagged (bitmask) enumerations based on the [BiplaneEnumBundle](https://github.com/yethee/BiplaneEnumBundle) ones.
- Symfony Form component integration with form types.
- Symfony Serializer component integration with a normalizer class.
- Doctrine DBAL integration with abstract classes in order to persist your enumeration in database.

# Installation

```sh
$ composer require elao/enum
```

Even if you're using the Symfony full stack framework, there is nothing more to do.   
We won't register anything in the Symfony DIC for now. Simply use provided classes.

# Usage

Declare your own enumeration by creating a class extending `Elao\Enum\Enum`:

```php
<?php

use Elao\Enum\Enum;

final class Gender extends Enum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function values(): array
    {
        return [
            self::UNKNOW, 
            self::MALE, 
            self::FEMALE
        ];
    }
}
```

> :memo: It's recommended to make your enums classes `final`, because it won't make sense to extend them in most situations (unless you're creating a new base enum type), and you won't need to mock an enum type.

Create a new instance of your enumeration:

```php
<?php
$enum = Gender::create(Gender::Male); 
```

You can easily retrieve the enumeration's value by using `$enum->getValue();`


## Readable enums

Sometimes, enums may be displayed to the user, or exported in a human readable way.  
Hence comes the `ReadableEnum`:

```php
<?php

use Elao\Enum\ReadableEnum;

final class Gender extends ReadableEnum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function values(): array
    {
        return [
            self::UNKNOW,
            self::MALE,
            self::FEMALE,
        ];
    }

    public static function readables(): array
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
(string) $enum; // returns 'Male'
```

If you're using a translation library, you can also simply return translation keys from the `ReadableEnumInterface::readables()` method:

```php
<?php

use Elao\Enum\ReadableEnum;

final class Gender extends ReadableEnum
{
    // ...
    
    public static function readables(): array
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

## Flagged enums

Flagged enumerations are used for bitwise operations.
Each value of the enumeration is a single bit flag and can be combined together into a valid bitmask in a single enum instance.

```php
<?php

use Elao\Enum\FlaggedEnum;

final class Permissions extends FlaggedEnum
{
    const EXECUTE = 1;
    const WRITE = 2;
    const READ = 4;

    // You can declare shortcuts for common bit flag combinations
    const ALL = self::EXECUTE | self::WRITE | self::READ;

    public static function values(): array
    {
        return [
            // Only declare valid bit flags:
            static::EXECUTE,
            static::WRITE,
            static::READ,
        ];
    }

    public static function readables(): array
    {
        return [
            static::EXECUTE => 'Execute',
            static::WRITE => 'Write',
            static::READ => 'Read',

            // You can define readable values for specific bit flag combinations:
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
$permissions->getFlags(); // Returns [2, 4] (=> [Permissions::EXECUTE, Permissions::WRITE])

$permissions = $permissions->removeFlags(Permissions::READ | Permissions::WRITE); // Returns a new instance without "read" and "write" flags
$permissions->getValue(); // Returns Permissions::NONE (0). Note: NONE is defined in parent class, FlaggedEnum.
$permissions->getFlags(); // Returns an empty array

$permissions = Permissions::create(Permissions::NONE); // Creates an empty bitmask instance
$permissions = $permissions->addFlags(Permissions::READ | Permissions::EXECUTE); // Returns a new instance with "read" and "execute" permissions
$permissions->hasFlag(Permissions::READ); // True
$permissions->hasFlag(Permissions::READ | Permissions::EXECUTE); // True
$permissions->hasFlag(Permissions::WRITE); // False
```

## Compare

Enumeration values are singletons: it means creating an enum instance will actually always return the exact same instance for a given value.
Thus, in order to compare two instances, you can simply use the strict comparison operator in order to check references:

```php
<?php
Gender::create(Gender::MALE) === Gender::create(Gender::FEMALE); // False
Gender::create(Gender::MALE) === Gender::create(Gender::MALE); // True
Permissions::create(Permissions::ALL) === Permissions::create(
    Permissions::READ | Permissions::WRITE | Permissions::EXECUTE
); // True
```

You can also override the `EnumInterface::equals(EnumInterface $enumToCompare)` in order to implement your own logic to determine if two instances should be considered the same.  
The default implementation compares both enum type (the class) and value.

```php
<?php
Gender::create(Gender::MALE)->equals(Gender::create(Gender::FEMALE)) // False
Gender::create(Gender::MALE)->equals(Gender::create(Gender::MALE)) // True
```

Lastly, you can simply compare an instance with a value by using the `EnumInterface::is($value)`:

```php
<?php
Gender::create(Gender::MALE)->is(Gender::FEMALE) // False
Gender::create(Gender::MALE)->is(Gender::MALE) // True
```

## Shortcuts

Inspired from [myclabs/php-enum](https://github.com/myclabs/php-enum#static-methods), you can use shortcuts to instantiate your enumerations, thanks to [PHP's `__callStatic` magic method](http://php.net/manual/en/language.oop5.overloading.php#object.callstatic):

```php
<?php
Gender::MALE(); // Returns an instance of Gender with the MALE value
```

We recommend you to use this method, if and only if, you and your team use an IDE (e.g PhpStorm) able to interpret the [`@method` tag](https://phpdoc.org/docs/latest/references/phpdoc/tags/method.html) in class definitions. Then, you can benefit from IDE completion by declaring the following:

```php
<?php

/**
 * @method static Gender UNKNOW()
 * @method static Gender MALE()
 * @method static Gender FEMALE()
 */
final class Gender extends ReadableEnum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';
    
    // ...
}
```

Otherwise, simply implement the static methods yourself.

# Integrations

## Doctrine

You can store the raw value of an enumeration in the database, but still manipulate it as an object from your entities by [creating a custom DBAL type](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html), from scratch.

However, this library can help you by providing abstract classes for both string and integer based enumerations.

### Create the DBAL type

First, create your DBAL type by extending either `AbstractEnumType` (string based enum) or `AbstractIntegerEnumType` (integer based enum, for flagged enums for instance):

```php
<?php

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;

final class GenderEnumType extends AbstractEnumType
{
    const NAME = 'gender';

    protected function getEnumClass(): string
    {
        return Gender::class;
    }

    public function getName()
    {
        return static::NAME;
    }
}
```

### Register the DBAL type

Then, you'll simply need to register your DBAL type:

#### Manually

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

#### Using the Doctrine Bundle with Symfony

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

### Mapping

When registering the custom types in the configuration, you specify a unique name for the mapping type and map it to the corresponding fully qualified class name. Now the new type can be used when mapping columns:

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

## Symfony Serializer component

Simply register the following normalizer inside the DIC configuration:

```yml
# services.yml
services:
    app.enum_normalizer:
        class: 'Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer'
        public: false
        tags: [{ name: serializer.normalizer }]
```

## Symfony Form component

### Simple enums

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

You can use any [`ChoiceType`](https://symfony.com/doc/current/reference/forms/types/choice.html) option as usual (for instance the `multiple` option).

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

### Flagged enums

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

# API

## Simple enum

Method | Static | Returns | Description
------ | ------ | ------- | -----------
`create($value)` | <kbd>Yes</kbd> | <kbd>static</kbd>| Creates a new instance of an enumeration.
`values()` | <kbd>Yes</kbd> | <kbd>array</kbd> | Should return any possible value for the enumeration.
`accepts($value)` | <kbd>Yes</kbd> | <kbd>bool</kbd> | True if the value is acceptable for this enumeration.
`instances()` | <kbd>Yes</kbd> | <kbd>static[]</kbd> | Instantiates and returns an array containing every enumeration instance for possible values.
`getValue()` | <kbd>No</kbd> | <kbd>mixed</kbd> | Returns the enumeration instance value.
`equals(EnumInterface $enum)` | <kbd>No</kbd> | <kbd>bool</kbd> | Determines whether two enumerations instances should be considered the same.
`is($value)` | <kbd>No</kbd> | <kbd>bool</kbd> | Determines if the enumeration instance value is equal to the given value.

## Readable enum

Method | Static | Returns | Description
------ | ------ | ------- | -----------
`readables()` | <kbd>Yes</kbd> | <kbd>string[]</kbd> | Should return an array of the human representations indexed by possible values.
`readableFor($value)` | <kbd>Yes</kbd> | <kbd>string</kbd> | Get the human representation for given enumeration value.
`getReadable()` | <kbd>No</kbd> | <kbd>string</kbd> | Get the human representation for the current instance.
`__toString()` | <kbd>No</kbd> | <kbd>string</kbd> | Allows to convert the instance to the human representation of the current value by casting it to a string.

## Flagged enum

Method | Static | Returns | Description
------ | ------ | ------- | -----------
`accepts($value)` | <kbd>Yes</kbd> | <kbd>bool</kbd> | Same as before, but accepts bit flags and bitmasks.
`readableForNone()` | <kbd>Yes</kbd> | <kbd>string</kbd> | Override this method to replace the default human representation of the "no flag" value.
`readableFor($value, string $separator = '; ')` | <kbd>Yes</kbd> | <kbd>string</kbd> | Same as before, but allows to specify a delimiter between single bit flags (if no human readable representation is found for the combination).
`getReadable(string $separator = '; ')` | <kbd>No</kbd> | <kbd>string</kbd> | Same as before, but with a delimiter option (see above).
`getFlags()` | <kbd>No</kbd> | <kbd>int[]</kbd> | Returns an array of bit flags set in the current enumeration instance.
`hasFlag(int $bitFlag)` | <kbd>No</kbd> | <kbd>bool</kbd> | True if the current instance has the given bit flag(s).
`addFlags(int $flags)` | <kbd>No</kbd> | <kbd>static</kbd> | Returns a new instance of the enumeration with additional flag(s).
`removeFlags(int $flags)` | <kbd>No</kbd> | <kbd>static</kbd> | Returns a new instance of the enumeration without given flag(s).
