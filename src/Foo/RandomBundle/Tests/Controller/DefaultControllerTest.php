<?php

namespace Foo\RandomBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    const INDEX_ROUTE = '/random';

    /**
     * Parameter combinations that should all cause exceptions.
     */
    public function randomInvalidParameterProvider() {
      return array(
        // One invalid parameter.
        array(array('foo' => 'bar')),
        // Two invalid parameters.
        array(array('foo' => 'bar', 'baz' => 'bing')),
        // Mix of valid and invalid parameters.
        array(array('generator' => 'hex', 'foo' => 'bar')),
      );
    }

    /**
     * Test that invalid parameters throw exceptions.
     *
     * @dataProvider randomInvalidParameterProvider
     */
    public function testRandomInvalidParameter($parameters)
    {
        $client = static::createClient();

        $client->request('GET', $this::INDEX_ROUTE, $parameters);

        $this->assertEquals($client->getResponse()->getStatusCode(), Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertThat(
          $client->getResponse()->getContent(),
          $this->stringContains('Unrecognised parameters: ')
        );
    }

    /**
     * Test that data returned by each generator is valid JSON data.
     */
    public function testRandomValidJSON() {
      // Test integers.
    }
}
