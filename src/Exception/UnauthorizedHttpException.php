<?php

declare(strict_types=1);

namespace Duyler\Http\Exception;

use Duyler\Http\Response\ResponseStatus;
use Throwable;

class UnauthorizedHttpException extends HttpException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(statusCode: ResponseStatus::STATUS_UNAUTHORIZED, previous: $previous);
    }
}
