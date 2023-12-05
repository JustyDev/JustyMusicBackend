<?php

namespace App;

use App\Providers\Entry\Entry;
use App\Providers\Utils\ErrorBuilder;
use Exception;

require __DIR__ . '/../vendor/autoload.php';

set_error_handler(function ($error_type, $error_message, $filename, $line_number) {

  if ($error_type == E_USER_ERROR) {

    ErrorBuilder::i('Ошибка исполнения сервера. Запрос не выполнен')
      ->setCode(-15)
      ->cleanBuffer()
      ->attach('info', [
        'type' => $error_type,
        'message' => $error_message,
        'file' => $filename,
        'line' => $line_number
      ])
      ->build();

  } //else {
//    $file = './logs/info/errors.txt';
//    $current = file_get_contents($file);
//    $current .= '[' . date("Y-m-d H:i:s") . ']: ' . $errMsg . "\n on " . $filename . "\n" . ' on line ' . $lineNum . ' with error level ' . $errno . "\n\n";
//    file_put_contents($file, $current);
  //}
});

error_reporting(E_ALL);

register_shutdown_function(function () {
  $error = error_get_last();
  if (is_array($error) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {

    try {

      ErrorBuilder::i('Сервис временно недоступен')
        ->setCode(-10)
        ->cleanBuffer()
        ->attach('debug_id', $error)
        ->build();

    } catch (Exception) {

      ErrorBuilder::i('Сервис временно недоступен')
        ->setCode(-100)
        ->cleanBuffer()
        ->attach('info', $error)
        ->build();
    }
  }
});

$app = new Entry();
$app->run();