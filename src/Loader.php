<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\Dto\Action;
use Duyler\Framework\Loader\LoaderServiceInterface;
use Duyler\Framework\Loader\PackageLoaderInterface;
use Duyler\Http\Action\CreateRequestAction;
use Duyler\Http\Action\StartRoutingAction;
use Duyler\Http\Provider\StartRoutingProvider;
use Duyler\Http\State\PrepareRequestStateHandler;
use Duyler\Http\State\ShareRequestStateHandler;
use Duyler\Router\CurrentRoute;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function __construct(private ContainerInterface $container) {}

    public function load(LoaderServiceInterface $loaderService): void
    {
        $requestAction = new Action(
            id: 'Http.CreateRequest',
            handler: CreateRequestAction::class,
            required: [
                'Http.StartRouting',
            ],
            argument: CurrentRoute::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: 'Http.StartRouting',
            handler: StartRoutingAction::class,
            providers: [
                StartRoutingAction::class => StartRoutingProvider::class,
            ],
            contract: CurrentRoute::class,
        );

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);

        $prepareRequest = $this->container->get(PrepareRequestStateHandler::class);
        $shareRequest = $this->container->get(ShareRequestStateHandler::class);

        $loaderService->addStateHandler($prepareRequest);
        $loaderService->addStateHandler($shareRequest);
    }
}
