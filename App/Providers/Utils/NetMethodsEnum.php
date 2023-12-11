<?php

namespace App\Providers\Utils;

enum NetMethodsEnum: string
{
  case GET = "GET";
  case POST = "POST";
  case PUT = "PUT";
  case DELETE = "DELETE";

  public function equals(NetMethodsEnum $method): bool
  {
    return $this->value === strtoupper($method->value);
  }

  public function isReal(): bool
  {
    return $this->equals(Net::method());
  }
}