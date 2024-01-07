<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\Http\Action\CreateRequestAction;
use Duyler\Http\Action\StartRoutingAction;
use Duyler\Http\Provider\StartRoutingProvider;
use Duyler\Http\State\PrepareRequestStateHandler;
use Duyler\Router\CurrentRoute;
use Duyler\Router\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function __construct(
        private RouteCollection $routeCollection,
    ) {}

    public function load(LoaderServiceInterface $loaderService): void
    {
        $requestAction = new Action(
            id: 'Http.CreateRequest',
            handler: CreateRequestAction::class,
            required: [
                'Http.StartRouting',
            ],
            providers: [
                CreateRequestAction::class => StartRoutingProvider::class,
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
            externalAccess: true,
        );

        $loaderService->getBuilder()->addAction($requestAction);
        $loaderService->getBuilder()->addAction($routingAction);

        $prepareRequest = $loaderService->getContainer()->get(PrepareRequestStateHandler::class);

        $loaderService->getBuilder()->addStateHandler($prepareRequest);
        $loaderService->getBuilder()->addSharedService($this->routeCollection);
    }
}
