<?php

namespace App\Providers\Utils;

class SUtils
{
  public static function clearPhoneNumber(string $number): string
  {
    return preg_replace("/[^0-9]/", "", $number);
  }

  public static function areValidPhone(string $number): string|bool
  {
    $cleared = self::clearPhoneNumber($number);

    if (strlen($cleared) !== 11) return false;

    return $cleared;
  }

  public static function formatPhoneNumber(string $number, string $country_code = "7"): string
  {
    $number = SUtils::clearPhoneNumber($number);

    $part1 = substr($number,-10,3);
    $part2 = substr($number,-7,3);
    $part3 = substr($number,-4,2);
    $part4 = substr($number,-2);

    return sprintf("+%s (%s) %s %s-%s", $country_code, $part1, $part2, $part3, $part4);
  }

  public static function spaceEveryChar(string $str): string
  {
    return implode(' ', str_split($str));
  }

  public static function genDefectedString(int $length = 8, string $inp = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
  {
    return substr(str_shuffle($inp), 0, $length);
  }
}