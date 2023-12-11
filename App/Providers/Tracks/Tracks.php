<?php

namespace App\Providers\Tracks;

use App\Objects\AuthorizedUser;
use App\Objects\Playlist;
use App\Objects\Track;
use App\Providers\Provider;
use App\Providers\Utils\AUtils;
use App\Providers\Utils\ErrorBuilder;
use App\Providers\Utils\Net;
use App\Providers\Utils\NetMethodsEnum;
use App\Providers\Utils\Response;

class Tracks extends Provider
{
  public function register(): void
  {
    $this->route('byPlaylist', NetMethodsEnum::GET, $this->byPlaylist(...));
    $this->route('listen', NetMethodsEnum::GET, $this->listen(...), false);
  }

  private function listen(): void
  {
    $track_id = (int)Net::param('track_id');
    $track = Track::findById($track_id);

    ErrorBuilder::i("Трек не найден")
      ->if(!$track)
      ->build();

    $local_path = $track->getLocalPath();

    header('Cache-Control: no-cache');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: audio/mp3');
    header('Content-Length: ' . filesize($local_path));
    header('Accept-Ranges: bytes');
    header('Content-Disposition: inline; filename="'.$track_id.'.wav"');

    readfile($local_path);
  }

  private function byPlaylist(AuthorizedUser $authed): void
  {
    $playlist_id = (int)Net::param('playlist_id');

    $user = $authed->getUser();
    $playlist = Playlist::findById($playlist_id);

    ErrorBuilder::i("Плейлист не найден")
      ->if(!$playlist)
      ->build();

    ErrorBuilder::i("Вы не можете просматривать чужие плейлисты")
      ->if($playlist->getId() !== 0 && $playlist->getCreatorId() !== $user->getId())
      ->build();

    Response::set([
      'playlist' => [
        'id' => $playlist->getId(),
        'creator_id' => $playlist->getCreatorId(),
        'title' => $playlist->getTitle(),
        'description' => $playlist->getDescription()
      ],
      'tracks' => AUtils::map($playlist->getTracks($user->getId()), function (Track $track) {
        return [
          'id' => $track->getId(),
          'title' => $track->getTitle(),
          'performers' => $track->getPerformers(),
          'is_explicit' => $track->isExplicit(),
          'is_liked' => true
        ];
      })
    ]);
  }
}