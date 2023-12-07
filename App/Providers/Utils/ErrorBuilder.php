<?php

namespace App\Providers\Utils;

class ErrorBuilder
{

  private string $message;
  private int $error_code;
  private int $error_http_code;
  private array $attachments;

  public function __construct(string $message)
  {
    $this->message = $message;
  }

  public static function i(string $message): ErrorBuilder
  {
    return new ErrorBuilder($message);
  }

  public function setCode(int $error_code): ErrorBuilder
  {
    $this->error_code = $error_code;
    return $this;
  }

  public function if(mixed $condition): ErrorBuilder
  {
    if (!$condition) return new EmptyErrorBuilder();
    return $this;
  }

  public function setHttpCode(int $error_http_code): ErrorBuilder
  {
    $this->error_http_code = $error_http_code;
    return $this;
  }

  public function cleanBuffer(): ErrorBuilder
  {
    while (ob_get_level()) {
      ob_end_clean();
    }

    return $this;
  }

  public function attach(string $name, mixed $value, bool $condition = true): ErrorBuilder
  {
    if ($condition) $this->attachments[$name] = $value;

    return $this;
  }

  public function build(): void
  {
    if ($this instanceof EmptyErrorBuilder) return;

    $response = [
      'message' => $this->message
    ];

    if (isset($this->error_code)) $response['code'] = $this->error_code;
    if (isset($this->error_http_code)) http_response_code($this->error_http_code);
    if (isset($this->attachments)) $response['attachments'] = $this->attachments;

    echo json_encode([
      'error' => $response
    ]);

    exit;
  }

}