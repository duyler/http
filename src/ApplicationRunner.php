<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Trigger;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Framework\Builder;
use Duyler\Router\RouteCollection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ApplicationRunner
{
    private ResponseTransmitter $responseTransmitter;
    private BusInterface $bus;
    private ContainerInterface $container;

    public function __construct()
    {
        $builder = new Builder();

        $this->container = $builder->getContainer();

        $this->responseTransmitter = $this->container->get(ResponseTransmitter::class);

        $builder->addSharedService($this->responseTransmitter);
        $builder->addSharedService($this->container->get(RouteCollection::class));

        $builder->loadPackages();
        $builder->loadBuild();

        $this->bus = $builder->build();
    }

    /**
     * @throws Throwable
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $trigger = new Trigger(
                id: 'Http.CreateRawRequest',
                status: ResultStatus::Success,
                data: $request,
                contract: ServerRequestInterface::class,
            );

            $this->bus->dispatchTrigger($trigger);
            $this->bus->run();
            $this->bus->reset();

            return $this->responseTransmitter->extract();
        } catch (Throwable $e) {
            $this->responseTransmitter->reset();
            throw $e;
        }
    }
}
