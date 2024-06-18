<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class InvalidSourceStructException extends IPPException
{
    public function __construct(string $message = "Invalid source structure", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INVALID_SOURCE_STRUCTURE, $previous, false);
    }
}

//than somewhere:
// throw new InvalidSourceStructExecution();