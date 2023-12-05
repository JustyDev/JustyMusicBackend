<?php

namespace App\Providers\Utils;

class Net
{
  private static object $data;

  public static function path(): NetPath
  {
    return new NetPath();
  }

  public static function setHeader(string $name, string $value): void
  {
    header($name . ': ' . $value);
  }

  public static function removeRepeatHeaders(array $headers): array
  {
    $array = [];
    foreach ($headers as $value) {
      $explode = explode(': ', $value);
      $array[$explode[0]] = $explode[1];
    }

    $r_arr = [];

    foreach ($array as $key => $val) {
      $r_arr[] = $key . ': ' . $val;
    }

    return $r_arr;
  }

  public static function param(string $name): mixed
  {
    if (!isset(self::$data)) {
      self::$data = json_decode(file_get_contents('php://input'));
    }

    return self::$data?->$name ?: false;
  }
}