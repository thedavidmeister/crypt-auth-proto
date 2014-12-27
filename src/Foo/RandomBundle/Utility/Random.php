<?php

namespace Foo\RandomBundle\Utility;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class Random
{

    public function __construct(\Symfony\Component\Validator\ValidatorInterface $validator)
    {
      if (!is_readable($this->path)) {
        throw new \Exception($this->path . ' must be readable for random number generation.');
      }

      $this->validator = $validator;

      // @see http://stackoverflow.com/questions/2353473/can-php-tell-if-the-server-os-it-64-bit
      $this->systemBits = strlen(decbin(~0));
    }

    /**
     * @Assert\EqualTo(
     *   value = "/dev/urandom",
     * )
     *
     * @var string
     *   The path to the OS provided source of randomness.
     */
    protected $path = '/dev/urandom';

    /**
     * @Assert\Range(
     *   min = 1,
     *   max = 1024,
     *   minMessage = "Must generate at least {{ limit }} byte(s) of randomness",
     *   maxMessage = "Cannot generate more than {{ limit }} bytes of randomness",
     *   invalidMessage = "The bytes of randomness to generate must be an integer",
     * )
     *
     * @var int
     *   The number of random bytes to produce with generate().
     */
    protected $bytes;

    protected $decMax;

    protected $systemBits;

    protected function validate()
    {

      $errors = $this->validator->validate($this);

      if (count($errors) > 0) {
        foreach ($errors as $error) {
          throw new \Exception($error->getMessage());
        }
      }

      return $this;

    }

    /**
     * Get bytes.
     */
    public function getBytes()
    {

      return $this->bytes;

    }

    /**
     * Set bytes.
     */
    public function setBytes($bytes)
    {

      $this->bytes = $bytes;

      $this->validate();

      return $this;

    }

    /**
     * Reads random bytes from /dev/urandom and returns as raw bytes or encoded.
     *
     * @param int $bytes
     *   The number of bytes to read.
     *
     * @return string
     *   A string of random bytes from /dev/urandom.
     */
    public function generate() {
      return file_get_contents($this->path, FALSE, NULL, 0, $this->getBytes());
    }

    /**
     * Decimal encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function integer()
    {
      // bindec() does not do what we want here, so convert a hex value instead.
      return hexdec($this->hex());
    }

    /**
     * Normalized (0-1) version of Random::bytes().
     *
     * @see Random::bytes().
     */
    public function normalized()
    {
      $integer = $this->dec();

      return (float) $integer / $max;

    }

    /**
     * Hexadecimal encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function hex()
    {
      return bin2hex($this->generate());
    }

    /**
     * Base 64 encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function base64()
    {
      return base64_encode($this->generate());
    }

}
