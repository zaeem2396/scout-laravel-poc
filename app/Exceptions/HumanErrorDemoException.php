<?php

namespace App\Exceptions;

use Exception;

class HumanErrorDemoException extends Exception
{
    public function __construct(string $message = 'User submitted invalid data (demo human error).')
    {
        parent::__construct($message);
    }
}
