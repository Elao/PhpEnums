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
    public function testReadableEnumForm()
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/form-type/readable-enum');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('form select[name="form[gender]"]'));
        $this->assertCount(3, $crawler->filter('form select[name="form[gender]"] option'));
        $this->assertSame([
            ['', 'unknown', 'Unknown'],
            ['selected', 'male', 'Male'],
            ['', 'female', 'Female'],
        ], $crawler->filter('form select[name="form[gender]"] option')->extract(['selected', 'value', '_text']));

        $form = $crawler->filter('form')->form();
        $form['form[gender]'] = Gender::FEMALE;

        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();

        $this->assertSame(['form[gender]' => Gender::FEMALE], $form->getValues());
    }

    public function testFlaggedEnumForm()
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/form-type/flagged-enum');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('form select[name="form[permissions][]"]'));
        $this->assertSame('multiple', $crawler->filter('form select[name="form[permissions][]"]')->attr('multiple'));
        $this->assertCount(3, $crawler->filter('form select[name="form[permissions][]"] option'));
        $this->assertSame([
            ['', '1', 'Execute'],
            ['', '2', 'Write'],
            ['selected', '4', 'Read'],
        ], $crawler->filter('form select[name="form[permissions][]"] option')->extract(['selected', 'value', '_text']));

        $form = $crawler->filter('form')->form();
        $form['form[permissions]'] = [Permissions::EXECUTE, Permissions::WRITE];

        // Submit multiple
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();

        $this->assertSame(['form[permissions]' => ['1', '2']], $form->getValues());

        // Submit empty
        $crawler = $client->submit($form);
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->filter('form')->form();
        $form['form[permissions]'] = [];

        $this->assertSame(['form[permissions]' => []], $form->getValues());
    }
}
