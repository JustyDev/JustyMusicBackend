<?php

namespace App\Providers\Utils;

class Net
{
  public static function path(): NetPath
  {
    return new NetPath();
  }

  public static function setHeader(string $name, string $value): void
  {
    header($name . ': ' . $value);
  }
}