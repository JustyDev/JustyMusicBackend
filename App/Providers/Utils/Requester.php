<?php

namespace App\Providers\Utils;

class Requester
{

  public static function get(
    $url,
    $data = [],
    $headers = [],
    $ssl = true,
    $timeout = false
  ): object|false
  {
    return self::clean('GET', $url, $data, $headers, $ssl, $timeout);
  }


  public static function clean(
    $method,
    $url,
    $data = [],
    $headers = [],
    $ssl = true,
    $timeout = false
  ): object|false
  {

    $ch = curl_init();

    $with_params = str_contains($url, '?') ? '&' : '?';

    if ($method == 'GET') $url .= $with_params . http_build_query($data);

    $headers = [
      "Content-Type: application/json",
      "Accept: application/json",
      ...$headers
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Net::removeRepeatHeaders($headers));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JustyMudic/1.0');
    curl_setopt($ch, CURLOPT_REFERER, 'justydev.ru');
    if ($timeout) curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if ($method != 'GET') {
      if (in_array('Content-Type: application/x-www-form-urlencoded', $headers)) {
        $post_data = http_build_query($data);
      } else {
        $post_data = json_encode($data);
      }

      if ($method == 'POST') curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }

    $response = curl_exec($ch);
    $err = curl_error($ch);

    //    if ($err) echo "cURL Error #:" . $err;
    if (!$response || $err) return false;

    return json_decode($response, false) ?: false;

  }
}