<?php

namespace App\Objects;

use App\Providers\Utils\Database\QueryBuilder;

class Code
{
  private int $id;
  private string $code;
  private string $recipient;
  private int $recipient_type; //CodeTypes
  private int $created_time;

  public static function findByRecipient(string $recipient): ?Code
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `codes` WHERE recipient = ?")
      ->bindString($recipient)
      ->asClass('\App\Objects\Code');
  }

  public static function findById(int $id): ?Code
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `codes` WHERE id = ?")
      ->bindInt($id)
      ->asClass('\App\Objects\Code');
  }

  public static function create(string $code, string $recipient, CodeTypes $type): ?Code
  {
    $created_code_id = QueryBuilder::i()
      ->query("INSERT INTO `codes` (`code`, `recipient`, `recipient_type`, `created_time`) VALUES (?,?,?,?)")
      ->bindString($code)
      ->bindString($recipient)
      ->bindInt($type->value)
      ->bindInt(time())
      ->insert();

    return self::findById($created_code_id);
  }

  public static function lastCode(string $recipient, CodeTypes $type): ?Code
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `codes` WHERE `recipient` = ? AND `recipient_type` = ? ORDER BY `id` DESC LIMIT 1")
      ->bindString($recipient)
      ->bindInt($type->value)
      ->asClass('\App\Objects\Code');
  }

  public function getCreatedTime(): int
  {
    return $this->created_time;
  }

  public static function areValid(string $code, int $digits = 6): bool
  {
    if (empty($code) || strlen($code) !== $digits) return false;

    return true;
  }

  public static function isExist(string $recipient, CodeTypes $type, string $code): bool
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `codes` WHERE recipient = ? AND code = ? AND recipient_type = ? AND (created_time + 3600) > ?")
      ->bindString($recipient)
      ->bindString($code)
      ->bindInt($type->value)
      ->bindInt(time())
      ->asExist();
  }

  public static function clear(string $recipient, CodeTypes $type): void
  {
    QueryBuilder::i()
      ->query("DELETE FROM `codes` WHERE recipient = ? AND recipient_type = ?")
      ->bindString($recipient)
      ->bindInt($type->value)
      ->delete();
  }
}