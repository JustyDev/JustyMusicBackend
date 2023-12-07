<?php

namespace App\Objects;

enum SessionTypes: int {
  case WEB_BROWSER = 1;
  case MOBILE_APP = 2;
  case DESKTOP_APP = 3;

}