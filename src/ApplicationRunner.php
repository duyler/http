<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\BusInterface;
use Duyler\Framework\Builder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ApplicationRunner
{
    private RequestProvider $requestProvider;
    private ResponseTransmitter $responseTransmitter;
    private BusInterface $bus;
    private ContainerInterface $container;

    public function __construct()
    {
        $builder = new Builder();

        $this->container = $builder->getContainer();

        $this->requestProvider = $this->container->get(RequestProvider::class);
        $this->responseTransmitter = $this->container->get(ResponseTransmitter::class);

        $builder->addSharedService($this->requestProvider);
        $builder->addSharedService($this->responseTransmitter);

        $builder->loadPackages();
        $builder->loadBuild();

        $this->bus = $builder->build();
    }

    /**
     * @throws Throwable
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->requestProvider->build($request);

        $this->bus->run();

        $response = $this->responseTransmitter->getResponse();

        $this->requestProvider->reset();
        $this->responseTransmitter->reset();

        return $response;
    }
}
