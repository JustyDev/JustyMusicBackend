<?php

namespace App\Providers\Auth;

use App\Config\Config;
use App\Objects\Code;
use App\Objects\CodeTypes;
use App\Objects\SessionTypes;
use App\Objects\User;
use App\Objects\UserPassword;
use App\Providers\Provider;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\IUtils;
use App\Providers\Utils\Net;
use App\Providers\Utils\NetMethodsEnum;
use App\Providers\Utils\Response;
use App\Providers\Utils\SUtils;

class Auth extends Provider
{

  public function register(): void
  {
    $this->route('register', NetMethodsEnum::GET, $this->registerAccount(...), false);
    $this->route('register', NetMethodsEnum::PUT, $this->checkCode(...), false);
    $this->route('register', NetMethodsEnum::POST, $this->registerComplete(...), false);

    $this->route('login', NetMethodsEnum::POST, $this->login(...), false);
  }

  private function login(): void
  {
    $phone_number = (string)Net::param('phone_number');
    $password = (string)Net::param('password');

    $phone_number = SUtils::areValidPhone($phone_number);

    ErrorBuilder::i('Переданные данные не верны')
      ->if(!$phone_number || !$password)
      ->build();

    $user = User::findByNumber($phone_number);

    ErrorBuilder::i('Неправильный логин или пароль')
      ->if(!$user || !UserPassword::equals($password, $user->getPassword()))
      ->build();

     //TODO: AUTO SELECT SESSION TYPE
    $session = $user->createSession(SessionTypes::MOBILE_APP, Config::SESSION_EXPIRES);

    Response::set([
      'id' => $user->getId(),
      'username' => $user->getUsername(),
      'number' => SUtils::formatPhoneNumber($user->getNumber()),
      'session' => [
        'id' => $session->getId(),
        'key' => $session->getKey(),
        'expires' => $session->getExpires(),
        'created_time' => $session->getCreatedTime(),
        'last_active' => $session->getLastActive()
      ]
    ]);
  }

  private function registerComplete(): void
  {

    $phone_number = (string)Net::param('phone_number');
    $code = (string)Net::param('code');
    $password = (string)Net::param('password');
    $username = (string)Net::param('username');

    $phone_number = SUtils::areValidPhone($phone_number);

    ErrorBuilder::i('Переданные данные не верны')
      ->if(!$phone_number || !Code::areValid($code, 5) || !$password || !$username)
      ->build();

    ErrorBuilder::i('Пользователь уже существует')
      ->if(User::findByUsername($username) || User::findByNumber($phone_number))
      ->build();

    ErrorBuilder::i('Неверный код')
      ->if(!Code::isExist($phone_number, CodeTypes::PHONE_NUMBER, $code))
      ->build();

    Code::clear($phone_number, CodeTypes::PHONE_NUMBER);

    $user = User::create(
      $phone_number,
      $username,
      $password
    );

    //TODO: AUTO SELECT SESSION TYPE
    $session = $user->createSession(SessionTypes::MOBILE_APP, Config::SESSION_EXPIRES);

    Response::set([
      'id' => $user->getId(),
      'username' => $user->getUsername(),
      'session' => [
        'id' => $session->getId(),
        'key' => $session->getKey(),
        'expires' => $session->getExpires(),
        'created_time' => $session->getCreatedTime(),
        'last_active' => $session->getLastActive()
      ]
    ]);
  }

  private function checkCode(): void
  {

    $phone_number = (string)Net::param('phone_number');
    $phone_number = SUtils::areValidPhone($phone_number);

    $code = (string)Net::param('code');

    ErrorBuilder::i('Переданные данные не верны')
      ->if(!$phone_number || !Code::areValid($code, 5))
      ->build();

    ErrorBuilder::i('Неверный код')
      ->if(!Code::isExist($phone_number, CodeTypes::PHONE_NUMBER, $code))
      ->build();

    Response::set([
      'type' => 'success',
      'phone_number' => $phone_number,
      'code' => $code
    ]);
  }

  private function registerAccount(): void
  {

    $phone_number = Net::param('phone_number');
    $phone_number = SUtils::areValidPhone($phone_number);

    ErrorBuilder::i('Не верный номер телефона')
      ->if(!$phone_number)
      ->build();

    $last_code = Code::lastCode($phone_number, CodeTypes::PHONE_NUMBER);

    ErrorBuilder::i('Подождите немного, прежде чем снова отправлять код')
      ->if($last_code && $last_code->getCreatedTime() + 60 > time())->build();

    $code = IUtils::genCode(5);
    //$code = SMSCenter::callCode($phone_number);
    //ErrorBuilder::i('Ошибка при отправке кода, попробуйте позже')->if(!$code)->build();

    Code::create($code, $phone_number, CodeTypes::PHONE_NUMBER);

    Response::set([
      'type' => 'success',
      'phone_number' => SUtils::formatPhoneNumber($phone_number)
    ]);
  }
}