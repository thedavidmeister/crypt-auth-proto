<?php

namespace Foo\RandomBundle\Utility;

use Symfony\Component\Debug\ExceptionHandler;


class Random {
    private static $maxbytes = 512;

    private static $minbytes = 1;

    /**
     * Reads random bytes from /dev/urandom and returns in base64.
     *
     * @param int $bytes
     *   The number of bytes to read.
     *
     * @return string
     *   A string of random bytes from /dev/urandom.
     */
    public static function urandomBase64($bytes) {

      $bytes = (int) $bytes;

      if ($bytes < self::$minbytes || $bytes > self::$maxbytes) {
        throw new \Exception('Number of bytes outside allowable range.');
      }

      return base64_encode(file_get_contents('/dev/urandom', FALSE, NULL, 0, $bytes));
    }
}
