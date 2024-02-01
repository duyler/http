<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\EventBus\Dto\Subscription;
use Duyler\Framework\Loader\LoaderServiceInterface;
use Duyler\Framework\Loader\PackageLoaderInterface;
use Duyler\Http\Action\StartRoutingAction;
use Duyler\Http\Factory\CreateRequestArgumentFactory;
use Duyler\Router\CurrentRoute;
use Duyler\Router\RouteCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    public function load(LoaderServiceInterface $loaderService): void
    {
        $requestAction = new Action(
            id: 'Http.CreateRequest',
            handler: fn(ServerRequestInterface $request) => $request,
            required: [
                'Http.StartRouting',
            ],
            argument: ServerRequestInterface::class,
            argumentFactory: CreateRequestArgumentFactory::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: 'Http.StartRouting',
            handler: StartRoutingAction::class,
            argument: ServerRequestInterface::class,
            contract: CurrentRoute::class,
        );

        $responseAction = new Action(
            id: 'Http.PersistResponse',
            handler: fn(ResponseInterface $response) => $response,
            argument: ResponseInterface::class,
            contract: ResponseInterface::class,
            externalAccess: true,
        );

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);
        $loaderService->addAction($responseAction);

        $loaderService->addSubscription(
            new Subscription(
                subjectId: 'Http.CreateRawRequest',
                actionId: 'Http.CreateRequest',
            )
        );

        $loaderService->addSubscription(
            new Subscription(
                subjectId: 'Http.CreateRawRequest',
                actionId: 'Http.StartRouting',
            )
        );

        $loaderService->addSubscription(
            new Subscription(
                subjectId: 'Http.CreateResponse',
                actionId: 'Http.PersistResponse',
            )
        );

        $loaderService->addSharedService($this->container->get(RouteCollection::class));
    }
}
