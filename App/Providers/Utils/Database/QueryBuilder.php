<?php

namespace App\Providers\Utils\Database;


use PDO;
use PDOStatement;
use stdClass;

class QueryBuilder
{
  private string $query = "";
  private array $params = [];

  private ?PDOStatement $stmt = null;

  public static function i(): QueryBuilder
  {
    return new QueryBuilder();
  }

  public function query(string $sql_query, bool $condition = true): QueryBuilder
  {
    if ($condition) $this->query .= $sql_query;
    return $this;
  }

  public function bindInt(int $value, bool $condition = true): QueryBuilder
  {
    if ($condition) $this->params[] = [
      'value' => $value,
      'type' => PDO::PARAM_INT
    ];

    return $this;
  }

  public function bindBool(bool $value, bool $condition = true): QueryBuilder
  {
    return $this->bindInt((int)$value, $condition);
  }

  public function bindString(string $value, bool $condition = true): QueryBuilder
  {
    if ($condition) $this->params[] = [
      'value' => $value,
      'type' => PDO::PARAM_STR
    ];

    return $this;
  }

  public function bindNamedInt(string $name, int $value, bool $condition = true): QueryBuilder
  {
    if ($condition) $this->params[] = [
      'value' => $value,
      'name' => $name,
      'type' => PDO::PARAM_INT
    ];

    return $this;
  }

  public function bindNamedBool(string $name, bool $value, bool $condition = true): QueryBuilder
  {
    return $this->bindNamedInt($name, (int)$value, $condition);
  }

  public function bindNamedString(string $name, string $value, bool $condition = true): QueryBuilder
  {
    if ($condition) $this->params[] = [
      'value' => $value,
      'name' => $name,
      'type' => PDO::PARAM_STR
    ];

    return $this;
  }

  protected function execute(): ?PDOStatement
  {

    $db = Database::get();

    $this->stmt = $db->prepare($this->query);

    $iter = 1;
    foreach ($this->params as $arr) {

      if (isset($arr['name'])) {
        $this->stmt->bindParam($arr['name'], $arr['value'], $arr['type']);
      } else {
        $this->stmt->bindValue($iter, $arr['value'], $arr['type']);
      }

      $iter++;

    }

    $this->stmt->execute();

    return $this->stmt ?: null;

  }

  public function asClass($namespace): ?object
  {
    return $this
      ->execute()
      ->fetchObject($namespace) ?: null;
  }

  public function asClassArray($namespace): array
  {
    return $this
      ->execute()
      ->fetchAll(PDO::FETCH_CLASS, $namespace) ?: [];
  }

  public function insert(): ?int
  {
    $this->execute();
    return Database::get()->lastInsertId() ?: null;
  }

}