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
use Exception;
use Imagick;

class Tracks extends Provider
{
  public function register(): void
  {
    $this->route('byPlaylist', NetMethodsEnum::GET, $this->byPlaylist(...));
    $this->route('listen', NetMethodsEnum::GET, $this->listen(...), false);
    $this->route('picture', NetMethodsEnum::GET, $this->picture(...), false);
  }

  private function picture(): void
  {
    $track_id = (int)Net::param('track_id');

    $height = Net::param('h') ?: null;
    $width = Net::param('w') ?: null;
    $size = Net::param('size') ?: null;

    if ($size) {
      $height = $width = (int) $size;
    }

    $track = Track::findById($track_id);

    ErrorBuilder::i("Трек не найден")
      ->if(!$track)
      ->build();

    $local_path = $track->getPictureLocalPath();

    Net::setHeader('Content-type', 'image/png');
    Net::setHeader('Content-Disposition', 'inline; filename="' . $track->getPictureHash() . '.png"');

    try {
      $thumb = new Imagick($local_path);
      $thumb->setImageFormat('png');

      if (!$height) {
        $height = $thumb->getImageHeight();
        if ($width) $height = $width * $thumb->getImageHeight() / $thumb->getImageWidth();
      }

      if (!$width) {
        $width = $thumb->getImageWidth();
        if ($height) $width = $height * $thumb->getImageWidth() / $thumb->getImageHeight();
      }

      if ($height) {
        $thumb->resizeImage($width, $height, Imagick::ALIGN_CENTER, 0);
      } else {
        $height = $thumb->getImageHeight();
      }

      if ($width) {
        $thumb->resizeImage($width, $height, Imagick::ALIGN_CENTER, 0);
      } //else {
        //$width = $thumb->getImageWidth();
      //}

      $size = $thumb->getSizeOffset();
      if ($size) Net::setHeader('Content-Length', $size);

      echo $thumb->getImageBlob();

    } catch (Exception $err) {
      Net::setHeader('Content-Type', 'application/json; charset=UTF-8');
      ErrorBuilder::i($err->getMessage())->build();
    }
  }

  private function listen(): void
  {
    $track_id = (int)Net::param('track_id');
    $track = Track::findById($track_id);

    ErrorBuilder::i("Трек не найден")
      ->if(!$track)
      ->build();

    $local_path = $track->getLocalPath();

    Net::setHeader('Cache-Control', 'no-cache');
    Net::setHeader('Content-Transfer-Encoding', 'binary');
    Net::setHeader('Content-Type', 'audio/mp3');
    Net::setHeader('Content-Length', filesize($local_path));
    Net::setHeader('Accept-Ranges', 'bytes');
    Net::setHeader('Content-Disposition', 'inline; filename="' . $track->getHash() . '.mp3"');

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
          'hash' => $track->getHash(),
          'picture_hash' => $track->getPictureHash(),
          'is_explicit' => $track->isExplicit(),
          'is_liked' => true
        ];
      })
    ]);
  }
}