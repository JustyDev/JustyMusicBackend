<?php

namespace App\Providers\Utils;

class NetPath
{
  private array $methods;

  public function __construct()
  {
    $url = explode("?", $_SERVER['REQUEST_URI'])[0];
    $this->methods = explode('/', $url);

    /*
     * Remove a first element, because REQUEST_URI starts with "/"
     * */

    array_shift($this->methods);
  }

  public function get(int $i): ?string
  {

    /*
     * Indexes = /{0}/{1}/{2} etc...
     * */

    return $this->methods[$i] ?? null;
  }
}