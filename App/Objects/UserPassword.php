<?php

namespace App\Objects;

class UserPassword
{
  public static function equals(string $password, string $hash): bool {
    return password_verify($password, $hash);
  }

  public static function hash(string $hash): string
  {
    return password_hash(
      $hash,
      PASSWORD_BCRYPT,
      [
        "cost" => 12
      ]
    );
  }
}