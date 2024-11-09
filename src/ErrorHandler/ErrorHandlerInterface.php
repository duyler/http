<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\EventBus\Dto\Log;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorHandlerInterface
{
    public function handle(Throwable $exception, Log $log): ResponseInterface;

    public function is(Throwable $exception): bool;
}
