<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Builder\Loader\LoaderServiceInterface;
use Duyler\Builder\Loader\PackageLoaderInterface;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\Action\Context\ActionContext;
use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\Dto\Result;
use Duyler\Http\Action\Request;
use Duyler\Http\Action\Router;
use Duyler\Http\Event\Response;
use Duyler\Http\Factory\CreateRequestArgumentFactory;
use Duyler\Http\State\HandleEndStateHandler;
use Duyler\Router\CurrentRoute;
use Duyler\Router\RouteCollection;
use Duyler\Router\Router as HttpRouter;
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
            id: Request::GetRequest,
            handler: fn(ActionContext $context) => $context->argument(),
            required: [
                Router::GetRoute,
            ],
            listen: ['Http.CreateRawRequest'],
            argument: ServerRequestInterface::class,
            argumentFactory: CreateRequestArgumentFactory::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: Router::GetRoute,
            handler: function (ActionContext $context) {
                /** @var ServerRequestInterface $request */
                $request = $context->argument();
                return $context->call(
                    function (HttpRouter $router, RouteCollection $routeCollection) use ($request) {
                        $currentRoute = $router->startRouting(
                            routeCollection: $routeCollection,
                            serverRequest: $request,
                        );

                        return Result::success($currentRoute);
                    },
                );
            },
            listen: ['Http.CreateRawRequest'],
            argument: ServerRequestInterface::class,
            contract: CurrentRoute::class,
        );

        $responseAction = new Action(
            id: 'Http.PersistResponse',
            handler: fn(ActionContext $context) => $context->argument(),
            listen: [Response::ResponseCreated],
            argument: ResponseInterface::class,
            contract: ResponseInterface::class,
            externalAccess: true,
        );

        $endStateHandler = new HandleEndStateHandler();

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);
        $loaderService->addAction($responseAction);

        $loaderService->addStateHandler($endStateHandler);

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
            id: Response::ResponseCreated,
            contract: ResponseInterface::class,
        ));

        $loaderService->addEvent(new Event(
            id: Response::ResponseHasBeenSent,
            contract: ResponseInterface::class,
        ));
    }
}
