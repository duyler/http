<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Throwable;

interface ErrorHandlerInterface
{
    public function handle(Throwable $exception): Error;

    public function is(Throwable $exception): bool;
}
