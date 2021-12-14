Elao Enumerations
=================
[![Latest Stable Version](https://poser.pugx.org/elao/enum/v/stable?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Total Downloads](https://poser.pugx.org/elao/enum/downloads?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Monthly Downloads](https://poser.pugx.org/elao/enum/d/monthly?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Tests](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml/badge.svg)](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml)
[![Coveralls](https://img.shields.io/coveralls/Elao/PhpEnums.svg?style=flat-square)](https://coveralls.io/github/Elao/PhpEnums)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/Elao/PhpEnums.svg?style=flat-square)](https://scrutinizer-ci.com/g/Elao/PhpEnums/?branch=master)
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

**📢  This project used to emulate enumerations before PHP 8.1.**  
For the 1.x documentation, [click here](https://github.com/Elao/PhpEnums/tree/1.x)

## Readable enums

Readable enums provides a way to expose human-readable labels for your enum cases, by adding a
new `ReadableEnumInterface` contract to your enums.

The easiest way to implement this interface is by using the [`ReadableEnumTrait`](src/ReadableEnumTrait.php) and
the [`EnumCase`](src/Attribute/EnumCase.php) attribute:

```php
namespace App\Enum;

use Elao\Enum\ReadableEnumInterface;
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

The following snippet shows how to render the human readable value of an enum:

```php
$enum = Suit::get(Suit::HEARTS);
$enum->getReadable(); // returns 'suit.hearts'
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
$enum = Suit::get(Suit::HEARTS);
$translator->trans($enum->getReadable(), locale: 'fr'); // returns 'Coeurs'
```

## Flag enums

Flagged enumerations are used for bitwise operations.
Each value of the enumeration is a single bit flag and can be combined together into a valid bitmask in a single enum instance.

```php
namespace App\Enum;

enum Permissions: int
{
    case Execute = 1 << 0;
    case Write = 1 << 1;
    case Read = 1 << 2;
}
```

Manipulate flags using a [`FlagBag`](src/FlagBag.php) instance:

```php
use App\Enum\Permissions;
use Elao\Enum\FlagBag;

$permissions = new FlagBag(Permissions::Execute, Permissions::Write, Permissions::Read);
$permissions = $permissions->withoutFlags(Permissions::Execute); // Returns an instance without "execute" flag

$permissions->getBits(); // Returns [2, 4]
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

## Integrations

### Symfony Form

Symfony already provides an [EnumType](https://symfony.com/doc/current/reference/forms/types/enum.html)
for allowing the user to choose one or more options defined in a PHP enumeration.  
It extends the ChoiceType field and defines the same options.

However, it uses the enum case name as label, which might not be convenient.  
Since this library specifically support readable enums, it ships its
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

### Doctrine

Given Doctrine DBAL and ORM [does not provide yet](https://github.com/doctrine/orm/issues/9021) a way to easily write
DBAL types for enums, this library provides some base classes to save your PHP backed enumerations in your database.

#### In a Symfony app

This configuration is equivalent to the following sections explaining how to create a custom Doctrine DBAL type:

```yaml
elao_enum:
  doctrine:
    types:
      App\Enum\Suit: ~ # Defaults to `{ class: App\Enum\Suit, default: null }`
      permissions: { class: App\Enum\Permission } # You can set a name different from the enum FQCN
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
        return Suit::class;
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
