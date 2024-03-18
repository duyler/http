<?php

declare(strict_types=1);

namespace Duyler\Http\Exception;

use Duyler\Http\Response\ResponseStatus;
use Exception;
use Throwable;

class HttpException extends Exception
{
    public function __construct(
        public int $statusCode,
        public string|null $reasonPhrase = null,
        public Throwable|null $previous = null,
    ) {
        parent::__construct(ResponseStatus::ERROR_PHRASES[$statusCode] ?? '', $statusCode, $previous);
    }
}
