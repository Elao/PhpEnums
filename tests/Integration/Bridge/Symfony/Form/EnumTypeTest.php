<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Integration\Bridge\Symfony\Form;

use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EnumTypeTest extends WebTestCase
{
    public function testReadableEnumForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/form-type/readable-enum');
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertCount(1, $crawler->filter('form select[name="form[gender]"]'));
        self::assertCount(3, $crawler->filter('form select[name="form[gender]"] option'));
        self::assertSame([
            ['', 'unknown', 'Unknown'],
            ['selected', 'male', 'Male'],
            ['', 'female', 'Female'],
        ], $crawler->filter('form select[name="form[gender]"] option')->extract(['selected', 'value', '_text']));

        $form = $crawler->filter('form')->form();
        $form['form[gender]'] = Gender::FEMALE;

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();

        self::assertSame(['form[gender]' => Gender::FEMALE], $form->getValues());
    }

    public function testFlaggedEnumForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/form-type/flagged-enum');
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertCount(1, $crawler->filter('form select[name="form[permissions][]"]'));
        self::assertSame('multiple', $crawler->filter('form select[name="form[permissions][]"]')->attr('multiple'));
        self::assertCount(3, $crawler->filter('form select[name="form[permissions][]"] option'));
        self::assertSame([
            ['', '1', 'Execute'],
            ['', '2', 'Write'],
            ['selected', '4', 'Read'],
        ], $crawler->filter('form select[name="form[permissions][]"] option')->extract(['selected', 'value', '_text']));

        $form = $crawler->filter('form')->form();
        $form['form[permissions]'] = [Permissions::EXECUTE, Permissions::WRITE];

        // Submit multiple
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();

        self::assertSame(['form[permissions]' => ['1', '2']], $form->getValues());

        // Submit empty
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();
        $form['form[permissions]'] = [];

        self::assertSame(['form[permissions]' => []], $form->getValues());
    }

    public function provideFormWithChoicesAsEnumValuesUrls(): iterable
    {
        yield 'ChoiceType with value to enum transformer' => ['/form-type/value-to-enum-transformer-choice-form'];
        yield 'EnumType with choices_as_enum_values' => ['/form-type/choices-as-enum-values-enum-form'];
    }

    /**
     * @dataProvider provideFormWithChoicesAsEnumValuesUrls
     */
    public function testWithChoicesAsEnumValues(string $url): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $url);

        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            ['selected', 'male', 'customMaleLabel'],
            ['', 'female', 'customFemaleLabel'],
        ], $crawler->filter('form select[name="form[gender]"] option')->extract(['selected', 'value', '_text']));
        self::assertSame([
            ['', '1', 'customOneLabel'],
            ['selected', '2', 'customSecondLabel'],
        ], $crawler->filter('form select[name="form[simpleEnum]"] option')->extract(['selected', 'value', '_text']));

        $form = $crawler->filter('form')->form();
        $form['form[gender]'] = 'female';
        $form['form[simpleEnum]'] = '1';

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();

        self::assertSame(['form[gender]' => 'female', 'form[simpleEnum]' => '1'], $form->getValues());
    }
}
