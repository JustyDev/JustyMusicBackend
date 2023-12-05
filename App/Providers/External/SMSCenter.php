<?php

namespace App\Providers\External;

use App\Config\Config;
use App\Providers\Utils\IUtils;
use App\Providers\Utils\Requester;
use App\Providers\Utils\SUtils;

class SMSCenter
{

  const API_URL = "https://smsc.ru/sys/send.php";


  protected static function sendCodeUrl(): string
  {
    return self::API_URL . "?login=" . Config::SMS_LOGIN . "&psw=" . Config::SMS_PASSWORD . "&fmt=3";
  }

  public static function callMessage(string $number, string $msg): ?object
  {

    $url = self::sendCodeUrl() . "&phones=" . $number . "&mes=" . $msg . "&call=1";

    $response = Requester::get($url);
    if (!$response || !$response->id) return null;

    return $response;

  }

  public static function callCode(string $number, int $digits = 6): ?string
  {

    $code = IUtils::genCode($digits);
    $msg = "Здравствуйте! Ваш код: " . SUtils::spaceEveryChar($code);

    if (self::callMessage($number, $msg)) return $code;

    return null;

  }

}