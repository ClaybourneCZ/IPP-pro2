<?php

namespace IPP\Student\exceptions;

use IPP\Core\ReturnCode;
use IPP\Core\Exception\IPPException;
use Throwable;

class SemanticException extends IPPException
{
    public function __construct(string $message = "Sematic error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::SEMANTIC_ERROR, $previous, false);
    }
}

//than somewhere:
// throw new SematicExceptionExecution();