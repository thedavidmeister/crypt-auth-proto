<?php

namespace Foo\RandomBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    /**
     * The route at which random numbers are generated.
     */
    const INDEX_ROUTE = '/random';

    /**
     * The minimum bytes the rng can produce in generate().
     *
     * @var integer
     */
    const MIN_BYTES = 1;

    /**
     * The maximum bytes the rng can produce in generate().
     * @var integer
     */
    const MAX_BYTES = 1024;

    /**
     * The number of bytes that random integers must be.
     *
     * @var integer
     */
    const INT_BYTES = 8;

    /**
     * The default number of bytes to generate if not set.
     */
    const DEFAULT_BYTES = 8;

    /**
     * Assert that the given parameters throws exceptions containing $string.
     *
     * @param string $parameters
     *   An array of parameters to pass to $this::INDEX_ROUTE.
     *
     * @param string $string
     *   The string that the exception page must contain.
     */
    public function assertThrowsException($parameters, $string) {
      $client = static::createClient();

      $client->request('GET', $this::INDEX_ROUTE, $parameters);

      $this->assertEquals($client->getResponse()->getStatusCode(), Response::HTTP_INTERNAL_SERVER_ERROR);
      $this->assertThat(
        $client->getResponse()->getContent(),
        $this->stringContains($string)
      );
    }

    /**
     * Parameter combinations that cause exceptions for integer generators.
     */
    public function randomInvalidIntegerParameterProvider() {
      $random_bytes = rand($this::MIN_BYTES, $this::MAX_BYTES);
      // We don't want to accidentally test the correct bytes and assert a fail.
      if ($random_bytes === $this::INT_BYTES) {
        $random_bytes++;
      }

      return array(
        // Integer with random bytes.
        array(array('generator' => 'integer', 'bytes' => $random_bytes)),
        // Normalized with random bytes.
        array(array('generator' => 'normalized', 'bytes' => $random_bytes)),
        // These are not valid as long as the default method is base on
        // integers (because integer generation must always be INT_BYTES).
        // MIN_BYTES only.
        array(array('bytes' => $this::MIN_BYTES)),
        // MAX_BYTES only.
        array(array('bytes' => $this::MAX_BYTES)),
        // Random bytes only.
        array(array('bytes' => $random_bytes)),
      );
    }

    /**
     * Test that invalid parameters for invalid integers throws exceptions.
     *
     * @dataProvider randomInvalidIntegerParameterProvider
     */
    public function testRandomInvalidIntegerParameter($parameters) {
      $this->assertThrowsException($parameters, 'Bytes must be set to 8 when generating random integers.');
    }

    /**
     * Parameter combinations that should all cause exceptions.
     */
    public function randomInvalidParameterProvider() {
      return array(
        // One invalid parameter.
        array(array('foo' => 'bar')),
        // Two invalid parameters.
        array(array('foo' => 'bar', 'baz' => 'bing')),
        // Mix of valid and invalid parameters using 'generator'.
        array(array('generator' => 'hex', 'foo' => 'bar')),
        // A different mix of valid/invalid parameters using 'bytes'.
        array(array('bytes' => '16', 'foo' => 'bar')),
      );
    }

    /**
     * Test that invalid parameters throw exceptions.
     *
     * @dataProvider randomInvalidParameterProvider
     */
    public function testRandomInvalidParameter($parameters)
    {
      $this->assertThrowsException($parameters, 'Unrecognised parameters: ');
    }

    /**
     * Parameter combinations for testing valid JSON responses.
     */
    public function randomValidJSONProvider() {
      $random_bytes = rand($this::MIN_BYTES, $this::MAX_BYTES);

      return array(
        // Integers.
        array(array('generator' => 'integer')),
        // Integers with bytes set.
        array(array('generator' => 'integer', 'bytes' => $this::INT_BYTES)),
        // Normalized.
        array(array('generator' => 'normalized')),
        // Normalized with bytes set.
        array(array('generator' => 'normalized', 'bytes' => $this::INT_BYTES)),
        // Hex.
        array(array('generator' => 'hex')),
        // Hex with default bytes.
        array(array('generator' => 'hex', 'bytes' => $this::DEFAULT_BYTES)),
        // Hex with random bytes.
        array(array('generator' => 'hex', 'bytes' => $random_bytes)),
        // Base64.
        array(array('generator' => 'base64')),
        // Base64 with default bytes.
        array(array('generator' => 'base64', 'bytes' => $this::DEFAULT_BYTES)),
        // Base64 with random bytes.
        array(array('generator' => 'base64', 'bytes' => $random_bytes)),
        // INT_BYTES only.
        array(array('bytes' => $this::INT_BYTES)),
        // DEFAULT_BYTES only.
        array(array('bytes' => $this::DEFAULT_BYTES)),
      );
    }

    /**
     * Test that data returned by each generator is valid JSON data.
     *
     * @dataProvider randomValidJSONProvider
     */
    public function testRandomValidJSON($parameters) {
        // print_r($parameters);
        $client = static::createClient();
        $client->request('GET', $this::INDEX_ROUTE, $parameters);
        $this->assertEquals($client->getResponse()->getStatusCode(), Response::HTTP_OK);
        $this->assertJson($client->getResponse()->getContent());
    }
}
