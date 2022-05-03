Elao Enumerations
=================
[![Latest Version](https://poser.pugx.org/elao/enum/v?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Total Downloads](https://poser.pugx.org/elao/enum/downloads?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Monthly Downloads](https://poser.pugx.org/elao/enum/d/monthly?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Tests](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml/badge.svg)](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml)
[![Coveralls](https://img.shields.io/coveralls/Elao/PhpEnums.svg?style=flat-square)](https://coveralls.io/github/Elao/PhpEnums)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/Elao/PhpEnums.svg?style=flat-square)](https://scrutinizer-ci.com/g/Elao/PhpEnums/?branch=2.x)
[![php](https://img.shields.io/badge/PHP-8.1-green.svg?style=flat-square "Available for PHP 8.1+")](http://php.net)

_Provides additional, opinionated features to the [PHP 8.1+ native enums](https://php.watch/versions/8.1/enums) as well
as specific integrations with frameworks and libraries._

```php
enum Suit: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('suit.hearts')]
    case Hearts = 'H';

    #[EnumCase('suit.diamonds')]
    case Diamonds = 'D';

    #[EnumCase('suit.clubs')]
    case Clubs = 'C';

    #[EnumCase('suit.spades')]
    case Spades = 'S';
}
```

---

<p align="center">
    <strong>📢  This project used to emulate enumerations before PHP 8.1.</strong><br/>
    For the 1.x documentation, <a href="https://github.com/Elao/PhpEnums/tree/1.x">click here</a>
    <br/><br/>
    You can also consult <a href="https://github.com/Elao/PhpEnums/issues/124">this issue</a> to follow objectives & progress for the V2 of this lib.
</p>

---

- Features
  - [Readable enums](#readable-enums)
  - [Flag enums](#flag-enums)
  - [Extra values](#extra-values)
- [Integrations](#integrations)
  - [Symfony Forms](#symfony-form)
  - [Symfony Controller Argument Resolver](#symfony-httpkernel)
  - Symfony VarDumper
  - [Doctrine ORM](#doctrine)
  - [Doctrine ODM](#doctrine-odm)
  - [Faker](#faker)

## Installation

```bash
composer require "elao/enum:^2.0@beta"
```

Or, in order to help and test latest changes:

```bash
composer require "elao/enum:^2.x-dev"
```

## Readable enums

Readable enums provide a way to expose human-readable labels for your enum cases, by adding a
new `ReadableEnumInterface` contract to your enums.

The easiest way to implement this interface is by using the [`ReadableEnumTrait`](src/ReadableEnumTrait.php) and
the [`EnumCase`](src/Attribute/EnumCase.php) attribute:

```php
namespace App\Enum;

use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;
use Elao\Enum\Attribute\EnumCase;

enum Suit: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('suit.hearts')]
    case Hearts = 'H';

    #[EnumCase('suit.diamonds')]
    case Diamonds = 'D';

    #[EnumCase('suit.clubs')]
    case Clubs = 'C';

    #[EnumCase('suit.spades')]
    case Spades = 'S';
}
```

The following snippet shows how to get the human readable value of an enum:

```php
Suit::Hearts->getReadable(); // returns 'suit.hearts'
```

It defines a proper contract to expose an enum case label instead of using the enum case internal name. Which is
especially useful if the locale to expose labels to your users differs from the one you're writing your code, as well as
for creating integrations with libraries requiring to expose such labels.

It's also especially useful in conjunction with a translation library
like [Symfony's Translation component](https://symfony.com/doc/current/translation.html), by using translation keys.

Given the following translation file:

```yaml
# translations/messages.fr.yaml
suit.hearts: 'Coeurs'
suit.diamonds: 'Carreaux'
suit.clubs: 'Piques'
suit.spades: 'Trèfles'
```

```php
$enum = Suit::Hearts;
$translator->trans($enum->getReadable(), locale: 'fr'); // returns 'Coeurs'
```

## Extra values

The `EnumCase` attributes also provides you a way to configure some extra attributes on your cases and access these easily with the `ExtrasTrait`:

```php
namespace App\Enum;

use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\Attribute\EnumCase;

enum Suit implements ReadableEnumInterface
{
    use ExtrasTrait;

    #[EnumCase(extras: ['icon' => 'fa-heart', 'color' => 'red'])]
    case Hearts;

    #[EnumCase(extras: ['icon' => 'fa-diamond', 'color' => 'red'])]
    case Diamonds;

    #[EnumCase(extras: ['icon' => 'fa-club', 'color' => 'black'])]
    case Clubs;

    #[EnumCase(extras: ['icon' => 'fa-spade', 'color' => 'black'])]
    case Spades;
}
```

Access these infos using `ExtrasTrait::getExtra(string $key, bool $throwOnMissingExtra = false): mixed`:

```php
Suit::Hearts->getExtra('color'); // 'red'
Suit::Spades->getExtra('icon'); // 'fa-spade'
Suit::Spades->getExtra('missing-key'); // null
Suit::Spades->getExtra('missing-key', true); // throws
```

or create your own interfaces/traits:

```php
interface RenderableEnumInterface 
{
    public function getColor(): string;
    public function getIcon(): string;
}

use Elao\Enum\ExtrasTrait;

trait RenderableEnumTrait
{
    use ExtrasTrait;

    public function getColor(): string
    {
        $this->getExtra('color', true);
    }
    
    public function getIcon(): string
    {
        $this->getExtra('icon', true);
    }
}

use Elao\Enum\Attribute\EnumCase;

enum Suit implements RenderableEnumInterface
{
    use RenderableEnumTrait;

    #[EnumCase(extras: ['icon' => 'fa-heart', 'color' => 'red'])]
    case Hearts;
    
    // […]
}

Suit::Hearts->getColor(); // 'red'
```

## Flag enums

Flagged enumerations are used for bitwise operations.

```php
namespace App\Enum;

enum Permissions: int
{
    case Execute = 1 << 0;
    case Write = 1 << 1;
    case Read = 1 << 2;
}
```

Each enumerated case is a bit flag and can be combined with other cases into a bitmask and manipulated 
using a [`FlagBag`](src/FlagBag.php) object:

```php
use App\Enum\Permissions;
use Elao\Enum\FlagBag;

$permissions = FlagBag::from(Permissions::Execute, Permissions::Write, Permissions::Read);
// same as:
$permissions = new FlagBag(Permissions::class, 7); 
// where 7 is the "encoded" bits value for:
Permissions::Execute->value | Permissions::Write->value | Permissions::Read->value // 7
// or initiate a bag with all its possible values using:
$permissions = FlagBag::fromAll(Permissions::class);

$permissions = $permissions->withoutFlags(Permissions::Execute); // Returns an instance without "execute" flag

$permissions->getValue(); // Returns 6, i.e: the encoded bits value
$permissions->getBits(); // Returns [2, 4], i.e: the decoded bits
$permissions->getFlags(); // Returns [Permissions::Write, Permissions::Read]

$permissions = $permissions->withoutFlags(Permissions::Read, Permissions::Write); // Returns an instance without "read" and "write" flags
$permissions->getBits(); // Returns []
$permissions->getFlags(); // Returns []

$permissions = new FlagBag(Permissions::class, FlagBag::NONE); // Returns an empty bag

$permissions = $permissions->withFlags(Permissions::Read, Permissions::Execute); // Returns an instance with "read" and "execute" flags

$permissions->hasFlags(Permissions::Read); // True
$permissions->hasFlags(Permissions::Read, Permissions::Execute); // True
$permissions->hasFlags(Permissions::Write); // False
```

Hence, using `FlagBag::getValue()` you can get an encoded value for any combination of flags from your enum, 
and use it for storage or communication between your processes.

## Integrations

### Symfony Form

Symfony already provides an [EnumType](https://symfony.com/doc/current/reference/forms/types/enum.html)
for allowing the user to choose one or more options defined in a PHP enumeration.  
It extends the ChoiceType field and defines the same options.

However, it uses the enum case name as label, which might not be convenient.  
Since this library specifically supports readable enums, it ships its
own [EnumType](src/Bridge/Symfony/Form/Type/EnumType.php), extending Symfony's one and using the human representation of
each case instead of their names.

Use it instead of Symfony's one:

```php
namespace App\Form\Type;

use App\Enum\Suit;
use Symfony\Component\Form\AbstractType;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('suit', EnumType::class, [
                'class' => Suit::class, 
                'expanded' => true,
            ])
        ;
    }

    // ...
}
```
#### FlagBag Form Type

If you want to use `FlagBag` in Symfony Forms, use the [FlagBagType](src/Bridge/Symfony/Form/Type/FlagBagType.php). This
type also extends Symfony [EnumType](https://symfony.com/doc/current/reference/forms/types/enum.html), but it transforms
form values to and from `FlagBag` instances.

```php
namespace App\Form\Type;

use App\Enum\Permissions;
use Symfony\Component\Form\AbstractType;
use Elao\Enum\Bridge\Symfony\Form\Type\FlagBagType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('permission', FlagBagType::class, [
                'class' => Permissions::class, 
            ])
        ;
    }

    // ...
}
```

### Symfony HttpKernel

#### Resolve controller arguments from route path

As of Symfony 6.1+, [backed enum cases will be resolved](https://github.com/symfony/symfony/pull/44831) from route path parameters:

```php
class CardController
{
    #[Route('/cards/{suit}')]
    public function list(Suit $suit): Response
    {
        // [...]
    }
}
```

➜ A call to `/cards/H` will resolve the `$suit` argument as the `Suit::Hearts` enum case.

If you're not yet using Symfony HttpKernel 6.1+, this library will still make this working by registering its own
resolver.

#### Resolve controller arguments from query or body

You can also resolve from query params or from the request body:

```php
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;

class DefaultController
{
    #[Route('/cards')]
    public function list(
        #[BackedEnumFromQuery]
        ?Suit $suit = null,
    ): Response
    {
        // [...]
    }
}
```

➜ A call to `/cards?suit=H` will resolve the `$suit` argument as the `Suit::Hearts` enum case.

Use `BackedEnumFromBody` to resolve from the request body (`$_POST`).

It also supports variadics:

```php
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;

class DefaultController
{
    #[Route('/cards')]
    public function list(
        #[BackedEnumFromQuery]
        ?Suit ...$suits = null,
    ): Response
    {
        // [...]
    }
}
```

➜ A call to `/cards?suits[]=H&suits[]=S` will resolve the `$suits` argument as `[Suit::Hearts, Suit::Spades]`.

### Doctrine

As of `doctrine/orm` 2.11, PHP 8.1 enum types [are supported natively](https://twitter.com/ogizanagi/status/1480456881265012736?s=20):

```php
#[Entity]
class Card
{
    #[Column(type: 'string', enumType: Suit::class)]
    public $suit;
}
```

⚠️ Unless you have specific needs for a DBAL type as described below, 
we recommend using the official ORM integration for backed enums.

PhpEnums however also provides some base classes to save your PHP backed enumerations in your database.
In a near future, custom DBAL classes for use-cases specific to this library, 
such as storing a flag bag or a collection of backed enum cases, would also be provided.

#### In a Symfony app

This configuration is equivalent to the following sections explaining how to create a custom Doctrine DBAL type:

```yaml
elao_enum:
  doctrine:
    types:
      App\Enum\Suit: ~ # Defaults to `{ class: App\Enum\Suit, default: null, type: single }`
      permissions: { class: App\Enum\Permission } # You can set a name different from the enum FQCN
      permissions_bag: { class: App\Enum\Permissions, type: flagbag } # values are stored as an int and retrieved as FlagBag object
      App\Enum\RequestStatus: { default: 200 } # Default value from enum cases, in case the db value is NULL
```

It'll actually generate & register the types classes for you, saving you from writing this boilerplate code.

#### Manually

Read the
[Doctrine DBAL docs](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html)
first.

Extend the [AbstractEnumType](src/Bridge/Doctrine/DBAL/Types/AbstractEnumType.php):

```php
namespace App\Doctrine\DBAL\Type;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;
use App\Enum\Suit;

class SuitType extends AbstractEnumType
{
    protected function getEnumClass(): string
    {
        return Suit::class; // By default, the enum FQCN is used as the DBAL type name as well
    }
}
```

In your application bootstrapping code:

```php
use App\Doctrine\DBAL\Type\SuitType;
use Doctrine\DBAL\Types\Type;

Type::addType(Suit::class, SuitType::class);
```

To convert the underlying database type of your new "Suit" type directly into an instance of `Suit` when performing
schema operations, the type has to be registered with the database platform as well:

```php
$conn = $em->getConnection();
$conn->getDatabasePlatform()->registerDoctrineTypeMapping(Suit::class, SuitType::class);
```

Then, use it as a column type:

```php
use App\Enum\Suit;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Card
{
    #[ORM\Column(Suit::class, nullable: false)]
    private Suit $field;
}
```


### Doctrine ODM

You can store enumeration values as string or integer in your MongoDB database and manipulate them as objects thanks to custom mapping types included in this library.

In a near future, custom ODM classes for use-cases specific to this library,
such as storing a flag bag or a collection of backed enum cases, would also be provided.

#### In a Symfony app

This configuration is equivalent to the following sections explaining how to create a custom Doctrine ODM type:

```yaml
elao_enum:
  doctrine_mongodb:
    types:
      App\Enum\Suit: ~ # Defaults to `{ class: App\Enum\Suit, type: single }`
      permissions: { class: App\Enum\Permission } # You can set a name different from the enum FQCN
      another: { class: App\Enum\AnotherEnum, type: collection } # values are stored as an array of integers or strings
      App\Enum\RequestStatus: { default: 200 } # Default value from enum cases, in case the db value is NULL
```

It'll actually generate & register the types classes for you, saving you from writing this boilerplate code.

#### Manually

Read the
[Doctrine ODM docs](https://www.doctrine-project.org/projects/doctrine-mongodb-bundle/en/current/config.html#custom-types)
first.

Extend the [AbstractEnumType](src/Bridge/Doctrine/ODM/Types/AbstractEnumType.php) or [AbstractCollectionEnumType](src/Bridge/Doctrine/ODM/Types/AbstractCollectionEnumType.php):

```php
namespace App\Doctrine\ODM\Type;

use Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractEnumType;
use App\Enum\Suit;

class SuitType extends AbstractEnumType
{
    protected function getEnumClass(): string
    {
        return Suit::class; // By default, the enum FQCN is used as the DBAL type name as well
    }
}
```

In your application bootstrapping code:

```php
use App\Doctrine\ODM\Type\SuitType;
use Doctrine\ODM\MongoDB\Types\Type;

Type::addType(Suit::class, SuitType::class);
```

#### Mapping

Now the new type can be used when mapping fields:

```php
use App\Enum\Suit;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Card
{
    #[MongoDB\Field(Suit::class)]
    private Suit $field;
}
```

### Faker

The PhpEnums library provides a faker `EnumProvider` allowing to select random enum cases:

```php
use \Elao\Enum\Bridge\Faker\Provider\EnumProvider;
 
$faker = new Faker\Generator();
$faker->addProvider(new EnumProvider());

$faker->randomEnum(Suit::class) // Select one of the Suit cases, e.g: `Suit::Hearts`
$faker->randomEnums(Suit::class, 2, min: 1) // Select between 1 and 2 enums cases, e.g: `[Suit::Hearts, Suit::Spades]`
$faker->randomEnums(Suit::class, 3, exact: true) // Select exactly 3 enums cases
```

Its constructor receives a mapping of enum types aliases as first argument:

```php
new EnumProvider([
    'Civility' => App\Enum\Civility::class,
    'Suit' => App\Enum\Suit::class,
]);
```

This is especially useful when using this provider with Nelmio Alice's DSL (_see next section_)

### Usage with Alice

If you're using the [nelmio/alice](https://github.com/nelmio/alice) package and its bundle in order to generate fixtures, 
you can register the Faker provider by using the `nelmio_alice.faker.generator`:

```yml
# config/services.yaml
services:
    Elao\Enum\Bridge\Faker\Provider\EnumProvider:
        arguments:
            - Civility: App\Enum\Civility
              Suit: App\Enum\Suit
        tags: ['nelmio_alice.faker.provider']
```

The following example shows how to use the provider within a [PHP fixture file](https://github.com/nelmio/alice/blob/master/doc/complete-reference.md#php):

```php
return [
    MyEntity::class => [
        'entity1' => [
            'civility' => Civility::MISTER // Select a specific case, using PHP directly
            'suit' => '<randomEnum(App\Enum\Suit)>' // Select a random case
            'suit' => '<randomEnum(Suit)>' // Select a random case, using the FQCN alias
            'permissions' => '<randomEnums(Permissions, 3, false, 1)>' // Select between 1 and 2 enums cases
            'permissions' => '<randomEnums(Permissions, 3, true)>' // Select exactly 3 enums cases
        ]
    ]
]
```
