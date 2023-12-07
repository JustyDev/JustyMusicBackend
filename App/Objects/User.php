<?php

namespace App\Objects;

use App\Providers\Utils\Database\QueryBuilder;
use App\Providers\Utils\Net;

class User
{
  private int $id;
  private string $number;
  private string $username;
  private string $password;
  private string $reg_ip;
  private string $last_ip;

  private int $reg_time;
  private int $last_active;

  public function getId(): int
  {
    return $this->id;
  }

  public static function findByNumber(string $phone_number): ?User
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `users` WHERE number = ?")
      ->bindString($phone_number)
      ->asClass('\App\Objects\User');
  }

  public static function findByUsername(string $username): ?User
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `users` WHERE username = ?")
      ->bindString($username)
      ->asClass('\App\Objects\User');
  }

  public static function findById(string $user_id): ?User
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `users` WHERE id = ?")
      ->bindString($user_id)
      ->asClass('\App\Objects\User');
  }

  public static function create($number, $username, $password): ?User
  {
    $ip = Net::ip();
    $t = time();

    $user_id = QueryBuilder::i()
      ->query("INSERT INTO `users` (`number`,`username`, `password`, `last_ip`, `reg_ip`, `reg_time`, `last_active`) VALUES (?,?,?,?,?,?,?)")
      ->bindString($number)
      ->bindString($username)
      ->bindString(UserPassword::hash($password))
      ->bindString($ip)
      ->bindString($ip)
      ->bindInt($t)
      ->bindInt($t)
      ->insert();

    return User::findById($user_id);
  }

  public function createSession(SessionTypes $type = SessionTypes::WEB_BROWSER, int $expires = 0): Session
  {
    return Session::create($this, $type, $expires);
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function getPassword(): string
  {
    return $this->password;
  }
}