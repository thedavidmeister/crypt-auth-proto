<?php

namespace Foo\RandomBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/random/10');

        $this->assertTrue($crawler->filter('html:contains("10")')->count() > 0);
    }
}
