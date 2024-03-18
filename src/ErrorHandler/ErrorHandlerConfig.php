<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

readonly class ErrorHandlerConfig
{
    public function __construct(
        /** @var string[] */
        public array $errorHandlers = []
    ) {}
}
