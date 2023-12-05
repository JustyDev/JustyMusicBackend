<?php

namespace App\Providers\Utils;

class EmptyErrorBuilder extends ErrorBuilder
{
  public function __construct()
  {
    parent::__construct('');
  }
}