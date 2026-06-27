<?php

namespace App\Exceptions;

use Exception;

class ObservabilityDemoException extends Exception
{
    public function __construct(string $message = 'Intentional observability demo exception.')
    {
        parent::__construct($message);
    }
}
