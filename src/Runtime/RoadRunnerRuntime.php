<?php

declare(strict_types=1);

namespace Duyler\Http\Runtime;

use Duyler\Builder\Builder;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Event;
use Duyler\Http\ErrorHandler\ErrorHandler;
use Duyler\Http\Exception\NotImplementedHttpException;
use Duyler\Http\RuntimeInterface;
use Duyler\Http\State\HandleResponseStateHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Throwable;

final class RoadRunnerRuntime implements RuntimeInterface
{
    private BusInterface $bus;
    private ErrorHandler $errorHandler;
    private ContainerInterface $container;
    private PSR7Worker $worker;

    public function __construct()
    {
        $worker = Worker::create();
        $factory = new Psr17Factory();

        $this->worker = new PSR7Worker($worker, $factory, $factory, $factory);

        $responseStateHandler = new HandleResponseStateHandler($this->worker);

        $builder = new Builder();
        $builder->addStateHandler($responseStateHandler);
        $builder->loadPackages();
        $builder->loadBuild();

        $this->container = $builder->getContainer();
        $this->errorHandler = $this->container->get(ErrorHandler::class);
        $this->bus = $builder->build();
    }

    public function run(): void
    {
        while ($request = $this->worker->waitRequest()) {
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

                $this->bus->reset();
                $this->container->finalize();

            } catch (Throwable $e) {
                $this->bus->reset();
                $this->container->finalize();
                $this->worker->respond($this->errorHandler->handle($e));
                $this->worker->getWorker()->error((string) $e);
            }
        }
    }
}
