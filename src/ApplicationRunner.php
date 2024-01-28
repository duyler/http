<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\BusInterface;
use Duyler\Framework\Builder;
use Duyler\Router\RouteCollection;
use Duyler\Router\Router;
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
            $this->requestProvider->build($request);

            $this->container->set($request);
            $this->container->bind([
                ServerRequestInterface::class => $request::class,
            ]);

            $this->bus->run();

            $response = $this->responseTransmitter->getResponse();
            $this->terminate();
            return $response;
        } catch (Throwable $e) {
            $this->terminate();
            throw $e;
        }
    }

    private function terminate(): void
    {
        $this->requestProvider->reset();
        $this->responseTransmitter->reset();
    }
}
