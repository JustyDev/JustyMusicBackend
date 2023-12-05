<?php

namespace App\Providers\Auth;

use App\Providers\Provider;
use App\Providers\Utils\Net;
use App\Providers\Utils\Response;

class Auth extends Provider {

  public function route(): void
  {
    match (Net::path()->get(1)) {
      'register' => $this->put($this->register(...))
    };
  }

  private function register(): void
  {


    Response::set([
      'message' => 'it works'
    ]);
  }
}