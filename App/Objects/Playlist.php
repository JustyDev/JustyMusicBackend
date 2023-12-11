<?php

namespace App\Objects;

use App\Providers\Utils\Database\QueryBuilder;

class Playlist
{
  private int $id = 0;
  private ?int $creator_id = null;
  private ?string $title = null;
  private ?string $description = null;
  private ?int $created_time = null;
  private ?int $updated_time = null;

  public function getId():int {
    return $this->id;
  }
  public function getCreatorId():?int {
    return $this->creator_id;
  }
  public function getTitle():?string {
    return $this->title;
  }

  public function getDescription():?string {
    return $this->description;
  }

  public function getCreatedTime():?int {
    return $this->created_time;
  }

  public function getUpdatedTime():?int {
    return $this->updated_time;
  }

  public function getTracks(?int $user_id = null):array {
    if (!$user_id) $user_id = $this->getCreatorId();
    return Track::findByPlaylistId($this->getId(), $user_id);
  }

  public static function findById(?int $id): ?Playlist
  {
    if (!$id || $id == 0) return new Playlist();

    return QueryBuilder::i()
      ->query("SELECT * FROM `playlists` WHERE id = ?")
      ->bindInt($id)
      ->asClass('\App\Objects\Playlist');
  }
}