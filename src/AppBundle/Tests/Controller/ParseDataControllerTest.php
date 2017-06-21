<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParseDataControllerTest extends WebTestCase
{
    public function testParse()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/parse');
    }

}
