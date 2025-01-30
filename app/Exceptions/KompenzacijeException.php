<?php

namespace App\Exceptions;

use Exception;
use Serializable;

class KompenzacijeException extends Exception implements Serializable {
        
    public $meta;

    public function __construct($message, $code = 400, $meta = []) {
        parent::__construct($message, $code, null);
        $this->meta = $meta;
    }

    public function serialize() {
        return serialize([$this->message, $this->code, $this->meta]);
    }

    public function unserialize($serialized) {
        list($message, $code, $meta) = unserialize($serialized);
        if ($meta) {
            $this->$meta = $meta;
        }
        parent::__construct($message, $code, null);
    }

    public function getMeta() {
        return $this->meta;
    }

}