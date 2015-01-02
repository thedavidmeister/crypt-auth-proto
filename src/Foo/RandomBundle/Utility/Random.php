<?php

namespace Foo\RandomBundle\Utility;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class Random
{

    public function __construct(\Symfony\Component\Validator\ValidatorInterface $validator)
    {
      // @todo - write tests for this.
      if (!is_readable($this::PATH)) {
        throw new \Exception($this::PATH . ' must be readable for random number generation.');
      }

      // This works by generating a binary string with all 1's (bitflip 0) and
      // then counting the number of 1's, which will be equal to the number of
      // bits used internally by the system.
      if (strlen(decbin(~0)) !== $this::SYSTEM_BITS) {
        throw new \Exception('Random numbers must be generated on a ' . $this::SYSTEM_BITS . ' bit system');
      }

      $this->validator = $validator;

      // Set default bytes to DEFAULT_BYTES.
      $this->setBytes($this::DEFAULT_BYTES);
    }

    /**
     * @var string
     *   The path to the OS provided source of randomness.
     */
    const PATH = '/dev/urandom';

    /**
     * @var int
     *   The number of bytes to enforce when generating a random integer. This
     *   number must equal the number of bits the current system is operating on
     *   in bytes (divided by 8) so that the data from the OS covers the full
     *   range of +/- PHP_INT_MAX.
     *
     *   For example, a 64 bit system should use 8 bytes of randomly generated
     *   data for integers. Setting this number too high causes PHP to return
     *   '0' for most generated integers as the data is outside the range of
     *   PHP_INT_MAX. Setting this number too low means that some integers will
     *   never be "picked" by the random number generator. Both situations
     *   seriously undermine the usefulness of generated numbers.
     *
     *   DO NOT CHANGE THIS VALUE WITHOUT A VERY GOOD REASON TO!
     *
     * @see $systemBits
     */
    const INT_BYTES = 8;

    /**
     * The default number of bytes to generate if not set.
     */
    const DEFAULT_BYTES = 8;

    /**
     * The default method to use for generation if not set.
     */
    const DEFAULT_METHOD = 'normalized';

    /**
     * @var int
     *   Number of bits that must be supported by the system PHP operates on.
     */
    const SYSTEM_BITS = 64;

    /**
     * @Assert\Range(
     *   min = 1,
     *   max = 1024,
     *   minMessage = "Must generate at least {{ limit }} byte(s) of randomness",
     *   maxMessage = "Cannot generate more than {{ limit }} bytes of randomness",
     *   invalidMessage = "The bytes of randomness to generate must be an integer",
     * )
     *
     * @Assert\NotNull()
     *
     * @var int
     *   The number of random bytes to produce with generate().
     */
    private $bytes;

    /**
     * Reads random bytes from /dev/urandom and returns as raw bytes.
     *
     * @return string
     *   A string of random binary data from /dev/urandom.
     */
    private function generate()
    {
        // Only generate values if validation passes.
        $this->validate();

        // Read bytes from /dev/urandom.
        return file_get_contents($this::PATH, FALSE, NULL, 0, $this->getBytes());
    }

    /**
     * Wrapper for Symfony validation.
     */
    public function validate()
    {
      $errors = $this->validator->validate($this);

      // Always throw an exception if validation fails.
      if (count($errors) > 0) {
        foreach ($errors as $error) {
          throw new \Exception($error->getMessage());
        }
      }

      return $this;
    }

    /**
     * Get bytes.
     *
     * @api
     */
    public function getBytes()
    {
      return $this->bytes;
    }

    /**
     * Set bytes.
     *
     * @api
     */
    public function setBytes($bytes)
    {
      $this->bytes = (int) $bytes;
      return $this;
    }

    /**
     * Get method names for all methods that generate data.
     */
    public function getMethods() {
        return array(
            'integer',
            'normalized',
            'hex',
            'base64',
        );
    }

    /**
     * Get method names that rely on integers (and thus have extra limitations).
     */
    public function getIntMethods() {
        return array(
            'integer',
            'normalized',
        );
    }

    /**
     * Integer from Random::bytes().
     *
     * @api
     * @see Random::bytes().
     */
    public function integer()
    {
      if ($this->getBytes() !== $this::INT_BYTES) {
        throw new \Exception('Bytes must be set to ' . $this::INT_BYTES . ' when generating random integers.');
      }

      // bindec() does not do what we want here, so convert a hex value instead.
      $dec = hexdec($this->hex());

      if (is_infinite($dec)) {
        throw new \Exception('Generated integers must not exceed the bounds of the system.');
      }

      return (int) $dec;
    }

    /**
     * Normalized (0-1) random float.
     *
     * @api
     * @see Random::bytes().
     */
    public function normalized()
    {
      return abs($this->integer() / PHP_INT_MAX);
    }

    /**
     * Hexadecimal encoded Random::bytes().
     *
     * @api
     * @see Random::bytes().
     */
    public function hex()
    {
      return bin2hex($this->generate());
    }

    /**
     * Base 64 encoded Random::bytes().
     *
     * @api
     * @see Random::bytes().
     */
    public function base64()
    {
      return base64_encode($this->generate());
    }

}
