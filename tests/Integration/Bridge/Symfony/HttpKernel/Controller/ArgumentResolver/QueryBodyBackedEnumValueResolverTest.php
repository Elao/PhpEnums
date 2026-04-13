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

class QueryBodyBackedEnumValueResolverTest extends WebTestCase
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
