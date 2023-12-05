<?php

namespace App\Providers\Utils\Database;

use App\Config\Config;
use App\Providers\Utils\ErrorBuilder;
use PDO;
use PDOException;

class Database
{

  private static ?PDO $db = null;

  public static function get(): ?PDO
  {
    if (!(self::$db instanceof PDO)) {
      self::connect(
        Config::DB_USER,
        Config::DB_PASS,
        Config::DB_HOST,
        Config::DB_NAME,
      );
    }

    return self::$db;
  }

  public static function connect(string $user, string $pass, string $host, string $db_name, $fetchMode = PDO::FETCH_OBJ): ?PDO
  {

    if (!$host || !$pass || !$user || !$db_name) return null;

    try {

      self::$db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $pass) ?? null;
      self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $fetchMode);
      self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return self::$db;

    } catch (PDOException $e) {

      ErrorBuilder::i('Сервис временно недоступен')
        ->attach('additional', $e->getMessage())
        ->build();

      return null;

    }
  }

}