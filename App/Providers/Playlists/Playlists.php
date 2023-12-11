<?php

namespace App\Providers\Playlists;

use App\Objects\Playlist;
use App\Providers\Provider;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\Net;
use App\Providers\Utils\NetMethodsEnum;
use App\Providers\Utils\Response;

class Playlists extends Provider
{
  public function register(): void
  {
    $this->route('byId', NetMethodsEnum::PUT, $this->getPlaylist(...));
  }

  private function getPlaylist(): void
  {
    $playlist_id = (int) Net::param('playlist_id');

    $playlist = Playlist::findById($playlist_id);

    ErrorBuilder::i("Плейлист не найден")
      ->if(!$playlist)
      ->build();

    Response::set([
      'id' => $playlist->getId(),
      'creator_id' => $playlist->getCreatorId(),
      'title' => $playlist->getTitle(),
      'description' => $playlist->getDescription()
    ]);
  }
}