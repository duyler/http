<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\DI\ContainerInterface;

class ErrorHandlerProvider
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ErrorHandlerConfig $errorHandlerConfig,
    ) {}

    /** @return ErrorHandlerInterface[]  */
    public function getHandlers(): array
    {
        $handlers = [];

        foreach ($this->errorHandlerConfig->errorHandlers as $errorHandler) {
            $handlers[] = $this->container->get($errorHandler);
        }

        return $handlers;
    }
}
