<?php

namespace Foo\RandomBundle\Tests\Utility;

use Foo\RandomBundle\Utility\Random;

class RandomTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Size of data set.
   *
   * This is a random number generator, so we can't test it "completely", all
   * we can do is attempt to get a representative sample of the data it
   * produces.
   *
   * @var integer
   */
  private $rounds = 1000;

  /**
   * The number of bytes that random integers must be.
   *
   * @var integer
   */
  private $intBytes = 8;

  /**
   * The minimum bytes the rng can produce in generate().
   *
   * @var integer
   */
  private $minBytes = 1;

  /**
   * The maximum bytes the rng can produce in generate().
   * @var integer
   */
  private $maxBytes = 1024;

  /**
   * Returns a dummy validator object.
   *
   * @return \Symfony\Component\Validator\ValidatorInterface
   */
  private function validatorDummy() {
    return $this->getMockBuilder('\Symfony\Component\Validator\ValidatorInterface')->getMock();
  }

  /**
   * Generates an array of $this->rounds random values using $method.
   *
   * @param string $method
   *   The public method of Random to call to generate values.
   *
   * @param null|int
   *   The number of bytes of data to generate per round. If not set, a random
   *   number of bytes between $minBytes and $maxBytes will be set per round.
   *
   * @return array
   *   An array of length $this->rounds of randomly generated data.
   */
  private function generateData($method, $bytes = null)
  {
    // We don't need real validation to generate the data so just use a dummy.
    $random = new Random($this->validatorDummy());

    // Set bytes if appropriate.
    if (isset($bytes)) {
      $random->setBytes($bytes);
    }

    // Build $this->rounds of random data.
    $i = 0;
    $data = array();
    while ($i < $this->rounds) {
      // If $bytes is not set, we generate a random number of bytes every round.
      if (!isset($bytes)) {
        $random->setBytes(rand($this->minBytes, $this->maxBytes));
      }

      $data[] = $random->{$method}();
      $i++;
    }

    return $data;
  }

  /**
   * Test that Random::integer() returns valid integers.
   */
  public function testIntegerReturnsIntegers()
  {
    // Generate integer data.
    $data = $this->generateData('integer', $this->intBytes);

    // Integer data should all be native PHP integer type.
    $this->assertContainsOnly('integer', $data);

    // While it is *possible* that the integer 0 is legitimately generated in a
    // small test run (small relative to PHP_MAX_INT) it is much, much more
    // likely that seeing exactly 0 means "infinity" was generated by the system
    // trying to convert more bits than it can internally represent
    // (usually either 32 or 64) and then casting "infinity" to an integer,
    // which results in 0.
    $this->assertNotContains(0, $data);
  }

  /**
   * Test that Random::normalized() produces floats roughly as expected.
   */
  public function testNormalizedReturnsNormalizedFloats() {
    // Generate normalized data.
    $data = $this->generateData('normalized', $this->intBytes);

    // Normalized data should all be native PHP float type.
    $this->assertContainsOnly('float', $data);

    // Normalized data should be between 0 and 1.
    $this->assertLessThanOrEqual(1, max($data));
    $this->assertGreaterThanOrEqual(0, min($data));

    // The average of normalized data should be about 0.5 and we should have
    // roughly the same number of values between 0 - 0.5 and 0.5 - 1.
    // This is NOT a substitute for statistical tests for "randomness" (we
    // have to rely on /dev/urandom working as advertised) but we try to raise
    // red flags if something is seriously wrong with our PHP implementation
    // leading to obviously skewed data.
    $average = array_reduce($data, function($carry, $item) { return $carry + $item; }, 0) / count($data);
    $delta = abs(0.5 - $average);
    // 5% tolerance on the mean seems about right for 1000 rounds. Feel free to
    // change this if it causes regular issues for testbots or base it on some
    // non-empirical, actual math.
    $tolerance = 0.05;
    $this->assertLessThanOrEqual($tolerance, $delta);

    $zero_to_half = array_filter($data, function($item) {
      return $item < 0.5;
    });

    $half_to_one = array_filter($data, function($item) {
      return $item > 0.5;
    });

    $delta = abs(count($zero_to_half) - count ($half_to_one));
    // 100 tolerance seems about right for 1000 rounds, values of 10-70 are
    // common. Feel free to base this on non-empirical, actual math.
    $tolerance = 100;
    $this->assertLessThanOrEqual($tolerance, $delta);
  }

}