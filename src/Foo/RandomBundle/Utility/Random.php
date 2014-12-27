<?php

namespace Foo\RandomBundle\Utility;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class Random
{

    protected $path = '/dev/urandom';

    public function __construct(\Symfony\Component\Validator\ValidatorInterface $validator) {

      $this->validator = $validator;

    }

    /**
     * @Assert\Range(
     *   min = 1,
     *   max = 1024,
     *   minMessage = "Must generate at least {{ limit }} byte(s) of randomness",
     *   maxMessage = "Cannot generate more than {{ limit }} bytes of randomness",
     *   invalidMessage = "The bytes of randomness to generate must be an integer",
     * )
     */
    protected $bytes;

    protected function validate() {

      $errors = $this->validator->validate($this);

      if (count($errors) > 0) {
        foreach ($errors as $error) {
          throw new \Exception($error->getMessage());
        }
      }

      return $this;

    }

    public function getBytes() {

      $this->validate();

      return $this->bytes;

    }

    /**
     * Sets the number of bytes of generated randomness for each call.
     */
    public function setBytes($bytes) {

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
    public function dec() {

      // bindec() does not do what we want here, so convert a hex value instead.
      return hexdec($this->hex());

    }

    /**
     * Normalized (0-1) version of Random::bytes().
     *
     * @see Random::bytes().
     */
    public function normalized() {

      $max = pow(2, ($bytes * 8));
      if (is_infinite($max)) {
        throw new \Exception('Too many bytes for float operations.');
      }
      $integer = $this->dec();

      return (float) $integer / $max;

    }

    /**
     * Hexadecimal encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function hex() {

      return bin2hex($this->generate());

    }

    /**
     * Base 64 encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function base64() {

      return base64_encode($this->generate());

    }

}
