<?php

namespace App\Providers;

use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\NetMethodsEnum;

abstract class Provider
{
  abstract public function route();

  public function get(callable $func): void
  {
    $this->acceptedMethod(NetMethodsEnum::GET);
    $this->processing($func);
  }

  public function post(callable $func): void
  {
    $this->acceptedMethod(NetMethodsEnum::POST);
    $this->processing($func);
  }

  public function put(callable $func): void
  {
    $this->acceptedMethod(NetMethodsEnum::PUT);
    $this->processing($func);
  }

  public function delete(callable $func): void
  {
    $this->acceptedMethod(NetMethodsEnum::DELETE);
    $this->processing($func);
  }

  private function acceptedMethod(NetMethodsEnum $method): void
  {
    ErrorBuilder::i('Не верный метод')
      ->if($method->value !== strtoupper($_SERVER['REQUEST_METHOD']))
      ->build();
  }

  private function processing(callable $func): void
  {
    $func();
  }
}