<?php

declare(strict_types=1);

namespace Duyler\Http\Exception;

use Duyler\Http\Response\ResponseStatus;
use Throwable;

class NotImplementedHttpException extends HttpException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(statusCode: ResponseStatus::STATUS_NOT_IMPLEMENTED, previous: $previous);
    }
}
