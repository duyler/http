<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\ActionBus\BusInterface;
use Duyler\ActionBus\Dto\Trigger;
use Duyler\Framework\Builder;
use Duyler\Http\ErrorHandler\ErrorHandler;
use Duyler\Http\Exception\NotImplementedHttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ApplicationRunner
{
    private BusInterface $bus;
    private ErrorHandler $errorHandler;

    public function __construct()
    {
        $builder = new Builder();
        $builder->loadPackages();
        $builder->loadBuild();

        $container = $builder->getContainer();
        $this->errorHandler = $container->get(ErrorHandler::class);

        $this->bus = $builder->build();
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $trigger = new Trigger(
                id: 'Http.CreateRawRequest',
                data: $request,
                contract: ServerRequestInterface::class,
            );

            $this->bus->dispatchTrigger($trigger);
            $this->bus->run();

            if (false === $this->bus->resultIsExists('Http.PersistResponse')) {
                throw new NotImplementedHttpException();
            }

            /** @var ResponseInterface $response */
            $response = $this->bus->getResult('Http.PersistResponse')->data;
            $this->bus->reset();
            return $response;
        } catch (Throwable $e) {
            $this->bus->reset();
            return $this->errorHandler->handle($e);
        }
    }
}
