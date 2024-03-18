<?php

declare(strict_types=1);

namespace Duyler\Http\Exception;

use Duyler\Http\Response\ResponseStatus;
use Throwable;

class ForbiddenHttpException extends HttpException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(statusCode: ResponseStatus::STATUS_FORBIDDEN, previous: $previous);
    }
}
