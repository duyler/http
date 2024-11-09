<?php

declare(strict_types=1);

namespace Duyler\Http\Runtime;

use Duyler\Builder\ApplicationBuilder;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Event;
use Duyler\Http\ErrorHandler\ErrorHandler;
use Duyler\Http\RuntimeConfig;
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

        $builder = new ApplicationBuilder();

        $this->container = $builder->getContainer();

        /** @var RuntimeConfig $config */
        $config = $this->container->get(RuntimeConfig::class);

        $responseStateHandler = new HandleResponseStateHandler($this->worker, $config);

        $this->bus = $builder->getBusBuilder()
            ->addStateHandler($responseStateHandler)
            ->loadPackages()
            ->loadBuild()
            ->build();

        $this->errorHandler = $this->container->get(ErrorHandler::class);
    }

    public function run(): void
    {
        while (true) {
            try {
                $request = $this->worker->waitRequest();

                if (null === $request) {
                    break;
                }

                $event = new Event(
                    id: 'Http.CreateRawRequest',
                    data: $request,
                );

                $this->bus->dispatchEvent($event);
                $this->bus->run();

            } catch (Throwable $e) {
                $this->worker->respond($this->errorHandler->handle($e, $this->bus->getLog()));
            } finally {
                $this->finalize();
            }
        }
    }

    private function finalize(): void
    {
        $this->bus->reset();
        $this->container->finalize();

        gc_collect_cycles();
    }
}
