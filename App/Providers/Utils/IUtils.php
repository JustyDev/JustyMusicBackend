<?php

namespace App\Providers\Utils;

class IUtils
{
  public static function genCode($digits = 6): string
  {
    $code = "";

    for ($i = 0; $i < $digits; $i++) {
      $code .= mt_rand(0, 9);
    }

    return $code;
  }
}