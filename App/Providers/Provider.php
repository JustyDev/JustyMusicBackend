<?php

namespace App\Providers;

use App\Objects\AuthorizedUser;
use App\Objects\Session;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\Net;
use App\Providers\Utils\NetMethodsEnum;

abstract class Provider
{
  abstract public function register();

  public function route(string $name, NetMethodsEnum $method, callable $func, bool $authed = true, int $path_index = 1): void
  {
    if (Net::path()->get($path_index) === $name && $method->isReal()) {

      if ($authed) {

        $access_token = Net::getAccessToken();
        $session = Session::findByKey($access_token);

        ErrorBuilder::i("Вам необходимо авторизоваться для получения этих данных")
          ->if(!$session)
          ->build();

        $func(AuthorizedUser::i($session));

        exit;
      }

      $func();
      exit;
    }
  }
}