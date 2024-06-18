<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class FrameAccessException extends IPPException
{
    public function __construct(string $message = "Invalid frame access", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::FRAME_ACCESS_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new FrameAccessExecution();