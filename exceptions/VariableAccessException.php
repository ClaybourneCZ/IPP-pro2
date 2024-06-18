<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class VariableAccessException extends IPPException
{
    public function __construct(string $message = "Invalid variable access", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::VARIABLE_ACCESS_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new VariableAccessExecution();