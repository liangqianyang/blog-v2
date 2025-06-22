<?php

namespace App\Component\Exception;

use Throwable;

class DebugException extends \Exception
{
    public function __construct($message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getName(): string
    {
        return 'DebugException';
    }
}
