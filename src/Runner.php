<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\BusInterface;
use Duyler\Framework\RunnerInterface;
use HttpSoft\Response\EmptyResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Runner implements RunnerInterface
{
    public const string NAME = 'http';

    private RequestProvider $requestProvider;
    private ResponseTransmitter $responseEmitter;
    private BusInterface $bus;
    private ContainerInterface $container;

    /**
     * @throws Throwable
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->container->set($request);
        $this->container->bind([ServerRequestInterface::class => $request::class]);
        $this->requestProvider->set($request);

        $this->bus->run();
        return $this->responseEmitter->getResponse();
    }

    #[Override]
    public function load(LoaderServiceInterface $loaderService): void
    {
        $this->requestProvider = new RequestProvider();
        $this->responseEmitter = new ResponseTransmitter(
            new EmptyResponse(404),
        );

        $loaderService->getContainer()->set($this->requestProvider);
        $loaderService->getContainer()->set($this->responseEmitter);

        $loaderService->getBuilder()->addSharedService($this->requestProvider);
        $loaderService->getBuilder()->addSharedService($this->responseEmitter);

        $this->container = $loaderService->getContainer();
    }

    #[Override]
    public function prepare(BusInterface $bus): void
    {
        $this->bus = $bus;
    }
}
