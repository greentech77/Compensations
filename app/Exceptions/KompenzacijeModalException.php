<?php

/**
 * Pokaže exception kot modal overlay.
 */

namespace App\Exceptions;

use \Exception;
use App\Exceptions\KompenzacijeException;

class KompenzacijeModalException extends KompenzacijeException {
        
    public function __construct($message, $code = 400, $meta = []) {
        parent::__construct($message, $code, $meta);
    }

}