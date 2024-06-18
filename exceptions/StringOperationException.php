<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class StringOperationException extends IPPException
{
    public function __construct(string $message = "String operation error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::STRING_OPERATION_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new OperandValueExecution();