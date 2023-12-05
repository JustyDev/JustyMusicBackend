<?php

namespace App\Providers\Utils;

class Response
{

  public static function set(mixed $data, bool $is_clean = false): never
  {

    ErrorBuilder::i('Пустой ответ')
      ->if(empty($data))
      ->build();

    echo json_encode($is_clean ? $data : [
      'response' => $data
    ]);

    exit;
  }

  public static function html(string $data): never
  {

    Net::setHeader('Content-Type', 'text/html');

    echo $data;

    exit;
  }
}