<?php

namespace App\Providers\Auth;

use App\Objects\Code;
use App\Objects\CodeTypes;
use App\Providers\External\SMSCenter;
use App\Providers\Provider;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\Net;
use App\Providers\Utils\Response;
use App\Providers\Utils\SUtils;

class Auth extends Provider
{

  public function route(): void
  {
    match (Net::path()->get(1)) {
      'register' => $this->get($this->register(...))
    };
  }

  private function register(): void
  {

    $phone_number = Net::param('phone_number');
    $phone_number = SUtils::clearPhoneNumber($phone_number);

    ErrorBuilder::i('Не верный номер телефона')
      ->if(strlen($phone_number) !== 11)
      ->build();

    $last_code = Code::lastCode($phone_number, CodeTypes::PHONE_NUMBER);

    ErrorBuilder::i('Подождите немного, прежде чем снова отправлять код')
      ->if($last_code && $last_code->getCreatedTime() + 60 > time())->build();

    $code = SMSCenter::callCode($phone_number);
    ErrorBuilder::i('Ошибка при отправке кода, попробуйте позже')->if(!$code)->build();

    Code::create($code, $phone_number, CodeTypes::PHONE_NUMBER);

    Response::set([
      'type' => 'success',
      'phone_number' => SUtils::formatPhoneNumber($phone_number)
    ]);
  }
}