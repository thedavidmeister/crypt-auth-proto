<?php

namespace Foo\RandomBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $i = 1;
        $j = 512;

        while ($i < $j) {
          $crawler = $client->request('GET', '/random/' . $i);
          print $client->getResponse()->getContent() . '|';
          $this->assertTrue(mb_strlen(json_decode($client->getResponse()->getContent(), TRUE)) === $i);
          $i++;
        }


    }
}
