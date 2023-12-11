<?php

namespace App\Providers\Utils;

use Exception;

class Net
{
  private static object $data;

  public static function path(): NetPath
  {
    return new NetPath();
  }

  public static function setHeader(string $name, string $value): void
  {
    header($name . ': ' . $value);
  }

  public static function getHeaderValue(string $headerName): string|false
  {
    $headers = getallheaders();
    $headers = array_change_key_case($headers);

    $headerName = strtolower($headerName);

    return trim($headers[$headerName]) ?: false;
  }

  public static function removeRepeatHeaders(array $headers): array
  {
    $array = [];
    foreach ($headers as $value) {
      $explode = explode(': ', $value);
      $array[$explode[0]] = $explode[1];
    }

    $r_arr = [];

    foreach ($array as $key => $val) {
      $r_arr[] = $key . ': ' . $val;
    }

    return $r_arr;
  }

  public static function method(): NetMethodsEnum
  {
    return NetMethodsEnum::from($_SERVER['REQUEST_METHOD']);
  }

  public static function param(string $name): mixed
  {
    try {

      if (Net::method()->equals(NetMethodsEnum::GET)) {
        return $_GET[$name] ?: null;
      }

      if (!isset(self::$data)) {
        self::$data = json_decode(file_get_contents('php://input')) ?: ((object)[]);
      }

      return self::$data?->$name ?: null;

    } catch (Exception) {
      return null;
    }
  }

  public static function ip(): string
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

  public static function getAccessToken(): ?string
  {
    $token = trim($_COOKIE['AccessToken']);

    if (!$token) {
      $token = Net::getHeaderValue('AccessToken');
    }

    if (!$token) {
      $token = Net::param('access_token');
    }

    return trim($token) ?: null;
  }
}