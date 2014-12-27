<?php

namespace Foo\RandomBundle\Utility;

use Symfony\Component\Debug\ExceptionHandler;


class Random
{

    protected $maxbytes = 1024;

    protected $minbytes = 1;

    protected $path = '/dev/urandom';

    /**
     * Reads random bytes from /dev/urandom and returns as raw bytes or encoded.
     *
     * @param int $bytes
     *   The number of bytes to read.
     *
     * @return string
     *   A string of random bytes from /dev/urandom.
     */
    public function bytes($bytes) {
      $bytes = (int) $bytes;
      $base = (int) $base;

      if ($bytes < $this->minbytes || $bytes > $this->maxbytes) {
        throw new \Exception('Number of bytes must be between ' . $this->minbytes .' and ' . $this->maxbytes . '.');
      }

      return file_get_contents($this->path, FALSE, NULL, 0, $bytes);
    }

    /**
     * Decimal encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function dec($bytes) {
      // bindec() does not do what we want here, so convert a hex value instead.
      return hexdec($this->hex($bytes));
    }

    /**
     * Normalized (0-1) version of Random::bytes().
     *
     * @see Random::bytes().
     */
    public function normalized($bytes) {
      $max = pow(2, ($bytes * 8));
      if (is_infinite($max)) {
        throw new \Exception('Too many bytes for float operations.');
      }
      $integer = $this->dec($bytes);

      return (float) $integer / $max;
    }

    /**
     * Hexadecimal encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function hex($bytes) {
      return bin2hex($this->bytes($bytes));
    }

    /**
     * Base 64 encoded Random::bytes().
     *
     * @see Random::bytes().
     */
    public function base64($bytes) {
      return base64_encode($this->bytes($bytes));
    }

}
