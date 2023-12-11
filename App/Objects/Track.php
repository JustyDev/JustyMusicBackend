<?php

namespace App\Objects;

use App\Providers\Utils\Database\QueryBuilder;

class Track
{
  private int $id;
  private string $title;
  private string $performers;
  private string $hash;
  private bool $is_explicit;

  public function getId(): int
  {
    return $this->id;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function getPerformers(): string
  {
    return $this->performers;
  }

  public function getHash(): string
  {
    return $this->hash;
  }

  public function isExplicit(): bool
  {
    return $this->is_explicit;
  }

  public static function findById(?int $track_id): ?Track
  {
    return QueryBuilder::i()
      ->query("SELECT * FROM `tracks` WHERE id = ?")
      ->bindInt($track_id)
      ->asClass('\App\Objects\Track');
  }

  public static function findByPlaylistId(int $playlist_id, ?int $user_id = null): array
  {
    if ($playlist_id == 0 && !$user_id) return [];
    return QueryBuilder::i()
      ->query("SELECT tr.* FROM playlists_tracks AS pt JOIN tracks AS tr ON tr.id = pt.track_id WHERE pt.playlist_id = ?")
      ->query(" AND pt.user_id = ?", $playlist_id == 0)
      ->bindInt($playlist_id)
      ->bindInt($user_id, $playlist_id == 0)
      ->asClassArray('\App\Objects\Track');
  }

  public function getLocalPath(): string
  {
    $hash = $this->getHash();
    $path = "/home/www/web/cdn.justydev.ru/music/";
    $path_folder = $path . substr($hash, 0, 2) . '/';
    if (!file_exists($path_folder)) mkdir($path_folder);
    return $path_folder . $hash;
  }
}