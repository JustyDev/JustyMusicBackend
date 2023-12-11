<?php

namespace App\Providers\Utils;

class AUtils
{
  public static function map(array|object|int $arr, callable $callback, bool $reverse = false): array
  {
    $res = [];
    if (is_object($arr)) return $callback($arr);
    if (is_int($arr)) {
      $cArr = $arr;
    } else {
      $cArr = count($arr);
    }
    for ($i = 0; $i < $cArr; $i++) {
      $callable = $callback($arr[$i], $i);
      if (!$callable && !is_numeric($callable)) continue;
      $res[] = $callable;
    }
    if ($reverse) return array_reverse($res);
    return $res;
  }

  public static function filter(array|object $arr, callable $callback): array
  {
    if (is_object($arr)) $arr = (array)$arr;
    $res = [];

    foreach ($arr as $value) {
      if ($callback($value)) $res[] = $value;
    }

    return $res;
  }
}