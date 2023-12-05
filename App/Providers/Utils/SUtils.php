<?php

namespace App\Providers\Utils;

class SUtils
{
  public static function clearPhoneNumber(string $number): string
  {
    return preg_replace("/[^0-9]/", "", $number);
  }

  public static function spaceEveryChar(string $str): string
  {
    return implode(' ', str_split($str));
  }
}