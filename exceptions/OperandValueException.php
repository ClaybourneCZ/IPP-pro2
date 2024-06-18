<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class OperandValueException extends IPPException
{
    public function __construct(string $message = "Invalid operand value", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::OPERAND_VALUE_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new OperandValueExecution();