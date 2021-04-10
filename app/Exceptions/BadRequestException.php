<?php

namespace App\Exceptions;

Class BadRequestException extends \Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if (empty($message)) {
            $message = 'Bad Request';
        }
        parent::__construct($message, $code, $previous);
    }
}
