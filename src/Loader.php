<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\Dto\Action;
use Duyler\EventBus\Dto\Subscription;
use Duyler\Framework\Loader\LoaderServiceInterface;
use Duyler\Framework\Loader\PackageLoaderInterface;
use Duyler\Http\Action\CreateRequestAction;
use Duyler\Http\Action\StartRoutingAction;
use Duyler\Http\Factory\CreateRequestArgumentFactory;
use Duyler\Router\CurrentRoute;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void
    {
        $requestAction = new Action(
            id: 'Http.CreateRequest',
            handler: CreateRequestAction::class,
            required: [
                'Http.StartRouting',
            ],
            argument: ServerRequestInterface::class,
            argumentFactory: CreateRequestArgumentFactory::class,
            contract: ServerRequestInterface::class,
            externalAccess: true,
            //repeatable: true,
        );

        $routingAction = new Action(
            id: 'Http.StartRouting',
            handler: StartRoutingAction::class,
            argument: ServerRequestInterface::class,
            contract: CurrentRoute::class,
            //repeatable: true,
        );

        $loaderService->addAction($requestAction);
        $loaderService->addAction($routingAction);

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
    }
}
