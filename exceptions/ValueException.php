<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class ValueException extends IPPException
{
    public function __construct(string $message = "Missing value", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::VALUE_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new ValueExecution();