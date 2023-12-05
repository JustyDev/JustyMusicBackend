<?php

namespace App\Providers\Utils;

enum NetMethodsEnum: string
{
  case GET = "GET";
  case POST = "POST";
  case PUT = "PUT";
  case DELETE = "DELETE";
}