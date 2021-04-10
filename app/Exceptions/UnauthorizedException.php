<?php

namespace App\Exceptions;

Class UnauthorizedException extends \Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if (empty($message)) {
            $message = 'Unauthorized';
        }
        parent::__construct($message, $code, $previous);
    }
}
