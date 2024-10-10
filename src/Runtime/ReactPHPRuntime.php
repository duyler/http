<?php

declare(strict_types=1);

namespace Duyler\Http\Runtime;

use Duyler\Builder\ApplicationBuilder;
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
        $builder = new ApplicationBuilder();
        $this->bus = $builder
            ->getBusBuilder()
            ->loadPackages()
            ->loadBuild()
            ->build();

        $this->container = $builder->getContainer();
        $this->errorHandler = $this->container->get(ErrorHandler::class);
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

                return $response;
            } catch (Throwable $e) {
                return $this->errorHandler->handle($e);
            } finally {
                $this->finalize();
            }
        });

        $socket = new SocketServer('0.0.0.0:80');
        $http->listen($socket);
    }

    private function finalize(): void
    {
        $this->bus->reset();
        $this->container->finalize();

        gc_collect_cycles();
    }
}
