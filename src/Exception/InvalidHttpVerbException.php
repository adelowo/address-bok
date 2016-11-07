<?php

namespace SeedStars\Exception;

use Exception;

class InvalidHttpVerbException extends Exception
{
    const UNSUPPORTED_HTTP_REQUEST_METHOD = "The verb, {{ verb }} is not supported ";

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->message = $this->formatMessage($message);
    }

    protected function formatMessage(string $message)
    {
        return str_replace("{{ verb }}", $message, self::UNSUPPORTED_HTTP_REQUEST_METHOD);
    }
}
