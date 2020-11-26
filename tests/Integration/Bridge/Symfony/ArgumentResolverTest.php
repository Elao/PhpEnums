<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Symfony;

use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class ArgumentResolverTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!interface_exists(ArgumentValueResolverInterface::class)) {
            self::markTestSkipped(
                sprintf('"%s" is not present in this Symfony version', ArgumentValueResolverInterface::class)
            );
        }
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEnumPassedInRequest(\Closure $setupClient, \Closure $assert): void
    {
        $client = static::createClient();
        $setupClient($client);
        $assert($client->getResponse());
    }

    public function requestProvider(): iterable
    {
        yield 'valid enum' => [
            function ($client) {
                $client->request(Request::METHOD_GET, '/enum-resolve/' . Gender::MALE);
            },
            function (Response $response) {
                $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals(Gender::MALE()->getReadable(), $response->getContent());
            },
        ];

        yield 'invalid enum' => [
            function ($client) {
                $client->request(Request::METHOD_GET, '/enum-resolve/bar');
            },
            function (Response $response) {
                $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            },
        ];

        yield 'valid variadic enum' => [
            function ($client) {
                $client->request(Request::METHOD_GET, '/enum-resolve-variadic', ['genders' => [Gender::MALE, Gender::FEMALE]]);
            },
            function (Response $response) {
                $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals(
                    sprintf('%s,%s', Gender::MALE()->getReadable(), Gender::FEMALE()->getReadable()),
                    $response->getContent()
                );
            },
        ];
    }
}
