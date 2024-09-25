<?php

declare(strict_types=1);

namespace Duyler\Http\ErrorHandler;

use Duyler\DI\ContainerInterface;

class ErrorHandlerProvider
{
    public function __construct(
        private ContainerInterface $container,
        private ErrorHandlerConfig $errorHandlerConfig,
    ) {}

    /** @return ErrorHandlerInterface[]  */
    public function getProviders(): array
    {
        $providers = [];

        foreach ($this->errorHandlerConfig->errorHandlers as $errorHandler) {
            $providers[] = $this->container->get($errorHandler);
        }

        return $providers;
    }
}
