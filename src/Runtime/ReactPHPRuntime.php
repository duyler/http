<?php

declare(strict_types=1);

namespace Duyler\Http\Runtime;

use Duyler\Builder\Builder;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Event;
use Duyler\Http\ErrorHandler\ErrorHandler;
use Duyler\Http\Exception\NotImplementedHttpException;
use Duyler\Http\RuntimeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Throwable;

final class ReactPHPRuntime implements RuntimeInterface
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

    public function run(): void
    {
        $http = new HttpServer(function (ServerRequestInterface $request) {
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
                $this->errorHandler->handle($e);
                echo (string) $e;
                throw $e;
            }
        });

        $socket = new SocketServer('0.0.0.0:80');
        $http->listen($socket);
    }
}
