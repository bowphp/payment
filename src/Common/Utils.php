<?php

namespace Bow\Payment\Common;

class Utils
{
  public static function generateRandomString($length = 10)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  /**
   * Generate a UUID v4
   *
   * @return string
   */
  public static function generateUuid(): string
  {
    // RFC 4122 v4 from a CSPRNG: these values become payment idempotency and
    // lookup keys, so they must be unguessable (rand()/mt_rand() are not).
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }
}
