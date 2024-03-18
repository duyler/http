<?php

declare(strict_types=1);

namespace Duyler\Http\Exception;

use Duyler\Http\Response\ResponseStatus;
use Throwable;

class BadRequestHttpException extends HttpException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(statusCode: ResponseStatus::STATUS_BAD_REQUEST, previous: $previous);
    }
}
