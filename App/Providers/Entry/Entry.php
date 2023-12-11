<?php

namespace App\Providers\Entry;

use App\Providers\Auth\Auth;
use App\Providers\Playlists\Playlists;
use App\Providers\Provider;
use App\Providers\Tracks\Tracks;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\Net;

final class Entry
{
  public function __construct()
  {
   /*
     * CORS Headers
     */

    Net::setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN'] ?: '*');
    Net::setHeader('Access-Control-Allow-Credentials', 'true');
    Net::setHeader('Access-Control-Max-Age', '3600');
    Net::setHeader('Access-Control-Expose-Headers', 'Content-Type, Authorization, API');
    Net::setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, API, X-Dev-Auth, AccessToken');
    Net::setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');

    /*
     * Security Headers
     * */

    Net::setHeader('X-Frame-Options', 'DENY');
    Net::setHeader('X-XSS-Protection', '1; mode=block');
    Net::setHeader('X-Content-Type-Options', 'nosniff');
    Net::setHeader('Server', 'JustyMusic/1.0');

    /*
     * Misc Headers
     */

    Net::setHeader('Content-Type', 'application/json; charset=UTF-8');
    Net::setHeader('X-Robots-Tag', 'noindex, nofollow');

    /*
     * Decline invalid requests
     */

    ErrorBuilder::i('Метод не поддерживается')
      ->if($_SERVER['REQUEST_METHOD'] == "OPTIONS")
      ->build();

    ErrorBuilder::i('Запрос отклонён')
      ->if(empty($_SERVER['HTTP_USER_AGENT']))
      ->build();

  }

  public function run(): void
  {
    $provider = match (Net::path()->get(0)) {
      'auth' => new Auth(),
      'tracks' => new Tracks(),
      'playlists' => new Playlists(),
      default => false
    };

    ErrorBuilder::i('Неизвестный запрос')
      ->if(!($provider instanceof Provider))
      ->build();

    $provider->register();

    ErrorBuilder::i('Запрос не найден')->build();

  }
}