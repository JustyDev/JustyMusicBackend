<?php

namespace App\Objects;

use App\Providers\Utils\Database\QueryBuilder;
use App\Providers\Utils\Net;
use App\Providers\Utils\SUtils;

class Session
{
  private int $id;
  private int $user_id;
  private int $type;
  private int $created_time;
  private int $expires;

  private string $key;
  private string $ip;
  private string $last_active;

  public static function findById(string $session_id): ?Session
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `sessions` WHERE id = ?")
      ->bindString($session_id)
      ->asClass('\App\Objects\Session');
  }

  public static function findByKey(string $key): ?Session
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `sessions` WHERE `key` = ?")
      ->bindString($key)
      ->asClass('\App\Objects\Session');
  }

  public static function create(User $user, SessionTypes $type = SessionTypes::WEB_BROWSER, int $expires = 0): ?Session
  {
    $t = time();

    $session_id = QueryBuilder::i()
      ->query("INSERT INTO `sessions` (`user_id`,`type`,`key`,`created_time`,`last_active`,`expires`,`ip`) VALUES (?,?,?,?,?,?,?)")
      ->bindInt($user->getId())
      ->bindInt($type->value)
      ->bindString(SUtils::genDefectedString(32))
      ->bindInt($t)
      ->bindInt($t)
      ->bindInt($expires)
      ->bindString(Net::ip())
      ->insert();

    return Session::findById($session_id);
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getUserId(): int
  {
    return $this->user_id;
  }

  public function getUser(): User
  {
    return User::findById($this->getId());
  }

  public function getKey(): string
  {
    return $this->key;
  }

  public function getExpires(): int
  {
    return $this->expires;
  }

  public function getCreatedTime(): int
  {
    return $this->created_time;
  }

  public function getLastActive(): int
  {
    return $this->last_active;
  }
}