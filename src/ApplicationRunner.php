<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\ActionBus\BusInterface;
use Duyler\ActionBus\Dto\Event;
use Duyler\DependencyInjection\ContainerInterface;
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
    private ContainerInterface $container;

    public function __construct()
    {
        $builder = new Builder();
        $builder->loadPackages();
        $builder->loadBuild();

        $this->container = $builder->getContainer();
        $this->errorHandler = $this->container->get(ErrorHandler::class);
        $this->bus = $builder->build();
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $event = new Event(
                id: 'Http.CreateRawRequest',
                data: $request,
            );

            $this->bus->dispatchEvent($event);
            $this->bus->run();

            if (false === $this->bus->resultIsExists('Http.PersistResponse')) {
                throw new NotImplementedHttpException();
            }

            /** @var ResponseInterface $response */
            $response = $this->bus->getResult('Http.PersistResponse')->data;
            $this->bus->reset();
            $this->container->finalize();
            return $response;
        } catch (Throwable $e) {
            $this->bus->reset();
            $this->container->finalize();
            return $this->errorHandler->handle($e);
        }
    }
}
