<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\Dto\Action;
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
            id: Http::GetRequest,
            handler: fn(ServerRequestInterface $request) => $request,
            required: [
                Http::GetRoute,
            ],
            triggeredOn: 'Http.CreateRawRequest',
            argument: ServerRequestInterface::class,
            argumentFactory: CreateRequestArgumentFactory::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: Http::GetRoute,
            handler: StartRoutingAction::class,
            triggeredOn: 'Http.CreateRawRequest',
            argument: ServerRequestInterface::class,
            contract: CurrentRoute::class,
        );

        $responseAction = new Action(
            id: 'Http.PersistResponse',
            handler: fn(ResponseInterface $response) => $response,
            triggeredOn: Http::CreateResponse,
            argument: ResponseInterface::class,
            contract: ResponseInterface::class,
            externalAccess: true,
        );

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);
        $loaderService->addAction($responseAction);

        $loaderService->addSharedService($this->container->get(RouteCollection::class));
    }
}
