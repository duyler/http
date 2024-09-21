<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\Action\Context\ActionContext;
use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\SharedService;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\Builder\Loader\LoaderServiceInterface;
use Duyler\Builder\Loader\PackageLoaderInterface;
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
            handler: fn(ActionContext $context) => $context->argument(),
            required: [
                Http::GetRoute,
            ],
            listen: ['Http.CreateRawRequest'],
            argument: ServerRequestInterface::class,
            argumentFactory: CreateRequestArgumentFactory::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: Http::GetRoute,
            handler: StartRoutingAction::class,
            listen: ['Http.CreateRawRequest'],
            argument: ServerRequestInterface::class,
            contract: CurrentRoute::class,
        );

        $responseAction = new Action(
            id: 'Http.PersistResponse',
            handler: fn(ActionContext $context) => $context->argument(),
            listen: [Http::CreateResponse],
            argument: ResponseInterface::class,
            contract: ResponseInterface::class,
            externalAccess: true,
        );

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);
        $loaderService->addAction($responseAction);

        $loaderService->addSharedService(
            new SharedService(
                class: RouteCollection::class,
                service: $this->container->get(RouteCollection::class),
            ),
        );

        $loaderService->addEvent(new Event(
            id: 'Http.CreateRawRequest',
            contract: ServerRequestInterface::class,
        ));

        $loaderService->addEvent(new Event(
            id: Http::CreateResponse,
            contract: ResponseInterface::class,
        ));
    }
}
