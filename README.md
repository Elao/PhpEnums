Elao Enumerations
=================
[![Latest Stable Version](https://poser.pugx.org/elao/enum/v/stable?format=flat-square)](https://packagist.org/packages/elao/enum) 
[![Total Downloads](https://poser.pugx.org/elao/enum/downloads?format=flat-square)](https://packagist.org/packages/elao/enum) 
[![Monthly Downloads](https://poser.pugx.org/elao/enum/d/monthly?format=flat-square)](https://packagist.org/packages/elao/enum)
[![Tests](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml/badge.svg)](https://github.com/Elao/PhpEnums/actions/workflows/ci.yml)
[![Coveralls](https://img.shields.io/coveralls/Elao/PhpEnums.svg?style=flat-square)](https://coveralls.io/github/Elao/PhpEnums)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/Elao/PhpEnums.svg?style=flat-square)](https://scrutinizer-ci.com/g/Elao/PhpEnums/?branch=master)
[![php](https://img.shields.io/badge/PHP-8.1-green.svg?style=flat-square "Available for PHP 8.1+")](http://php.net)

```php
<?php

enum Suit: string implements ReadableEnumInterface
{
    use ReadableEnumFromAttributesTrait;

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
