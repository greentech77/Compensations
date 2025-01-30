<?php

/**
 * Pokaže exception kot toast.
 */

namespace App\Exceptions;

use App\Exceptions\KompenzacijeException;

class KompenzacijeToastException extends KompenzacijeException {
        
    public function __construct($message, $code = 400, $meta = []) {
        parent::__construct($message, $code, $meta);
    }

}