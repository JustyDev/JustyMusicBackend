<?php

namespace App\Objects;

class AuthorizedUser
{
  private Session $session;
  private User $user;

  public function __construct(Session $session)
  {
    $this->session = $session;
  }

  public static function i(Session $session): AuthorizedUser
  {
    return new AuthorizedUser($session);
  }

  public function getSession(): Session
  {
    return $this->session;
  }

  public function getUser(): User
  {
    if (!isset($this->user)) $this->user = $this->getSession()->getUser();
    return $this->user;
  }
}