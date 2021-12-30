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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                $this->expectException(NotFoundHttpException::class);
                $this->expectExceptionMessage('Could not resolve the "App\Enum\Suit $suit" controller argument: "foo" is not a valid backing value for enum "App\Enum\Suit"');

                $client->request(Request::METHOD_GET, '/resolver/from-attributes/foo');
            },
        ];

        yield 'nullable' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-attributes-nullable');
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                null
                DUMP, $response->getContent());
            },
        ];

        yield 'with default' => [
            function (KernelBrowser $client) {
                $client->request(Request::METHOD_GET, '/resolver/from-attributes-with-default');
            },
            function (Response $response) {
                self::assertSame(Response::HTTP_OK, $response->getStatusCode());
                self::assertSame(<<<DUMP
                App\Enum\Suit {
                  +name: "Spades"
                  +value: "S"
                  readable: "suit.spades"
                }
                DUMP, $response->getContent());
            },
        ];
    }
}
