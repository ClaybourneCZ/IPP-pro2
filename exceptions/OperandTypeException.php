<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class OperandTypeException extends IPPException
{
    public function __construct(string $message = "Invalid type of operand/s", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::OPERAND_TYPE_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new OperandTypeExecution();