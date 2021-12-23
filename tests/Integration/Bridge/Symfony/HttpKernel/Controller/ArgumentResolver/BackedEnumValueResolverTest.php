<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver;

use App\Enum\Suit;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BackedEnumValueResolverTest extends WebTestCase
{
    /**
     * @dataProvider requestProvider
     */
    public function testResolver(
        callable $request,
        ?callable $assert = null,
    ): void {
        $client = static::createClient();
        $client->catchExceptions(false);

        \Closure::bind($request, $this)($client);

        if ($assert) {
            \Closure::bind($assert, $this)($client->getResponse());
        }
    }

    public function requestProvider(): iterable
    {
        yield 'from attributes' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-attributes/' . Suit::Hearts->value);
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Hearts"
                  +value: "H"
                  readable: "suit.hearts"
                }
                DUMP, $response->getContent());
            },
        ];

        yield 'invalid value' => [
            function (KernelBrowser $client) {
                $this->expectException(BadRequestHttpException::class);
                $this->expectExceptionMessage('Enum type "App\Enum\Suit" does not accept value "foo"');

                $client->request(Request::METHOD_GET, '/resolver/from-attributes/foo');
            },
        ];

        yield 'from query' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-query?' . http_build_query([
                    'suit' => Suit::Hearts->value,
                ]));
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Hearts"
                  +value: "H"
                  readable: "suit.hearts"
                }
                DUMP, $response->getContent());
            },
        ];

        yield 'from query nullable' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-query-nullable?' . http_build_query([
                    'suit' => '',
                ]));
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                null
                DUMP, $response->getContent());
            },
        ];

        yield 'from query with default' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-query-with-default');
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Hearts"
                  +value: "H"
                  readable: "suit.hearts"
                }
                DUMP, $response->getContent());
            },
        ];

        yield 'from query with default uses null' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-query-with-default?' . http_build_query([
                    'suit' => '',
                ]));
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                null
                DUMP, $response->getContent());
            },
        ];

        yield 'from query with default, non-nullable' => [
            function (KernelBrowser $client) {
                // this one will fail with:
                // Symfony\Component\DependencyInjection\Exception\RuntimeException: Cannot autowire argument $suit of "App\Controller\BackedEnumValueResolverController::fromQueryWithDefaultNonNullable()": it references class "App\Enum\Suit" but no such service exists.
                self::markTestSkipped('Skipped: requires Symfony fix. See https://github.com/symfony/symfony/pull/44826');

                $client->request(Request::METHOD_GET, '/resolver/from-query-with-default-non-nullable?' . http_build_query([
                    'suit' => '',
                ]));
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Hearts"
                  +value: "H"
                  readable: "suit.hearts"
                }
                DUMP, $response->getContent());
            },
        ];

        yield 'from query with variadics' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-query-variadics?' . http_build_query([
                    'suit' => [Suit::Hearts->value, Suit::Spades->value],
                ]));
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                [
                  App\Enum\Suit {
                    +name: "Hearts"
                    +value: "H"
                    readable: "suit.hearts"
                  }
                  App\Enum\Suit {
                    +name: "Spades"
                    +value: "S"
                    readable: "suit.spades"
                  }
                ]
                DUMP, $response->getContent());
            },
        ];

        yield 'from body' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_POST, '/resolver/from-body', [
                    'suit' => Suit::Hearts->value,
                ]);
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Hearts"
                  +value: "H"
                  readable: "suit.hearts"
                }
                DUMP, $response->getContent());
            },
        ];

        yield 'from body with variadics' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_POST, '/resolver/from-body-variadics', [
                    'suit' => [Suit::Hearts->value, Suit::Spades->value],
                ]);
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                [
                  App\Enum\Suit {
                    +name: "Hearts"
                    +value: "H"
                    readable: "suit.hearts"
                  }
                  App\Enum\Suit {
                    +name: "Spades"
                    +value: "S"
                    readable: "suit.spades"
                  }
                ]
                DUMP, $response->getContent());
            },
        ];
    }
}
