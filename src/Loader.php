<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\Http\Action\CreateRequestAction;
use Duyler\Http\Action\StartRoutingAction;
use Duyler\Http\Action\StartRoutingFallbackAction;
use Duyler\Http\Provider\RouterRequestProvider;
use Duyler\Router\Result;
use Duyler\Router\Router;
use HttpSoft\Message\ServerRequest;
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
            classMap: [
                ServerRequestInterface::class => ServerRequest::class
            ],
            arguments: [
                'result' => Result::class,
            ],
            contract: ServerRequestInterface::class,
            externalAccess: true,
        );

        $routingAction = new Action(
            id: 'Http.StartRouting',
            handler: StartRoutingAction::class,
            classMap: [
                ServerRequestInterface::class => ServerRequest::class
            ],
            providers: [
                Router::class => RouterRequestProvider::class
            ],
            contract: Result::class,
            externalAccess: true,
            continueIfFail: true,
        );

        $loaderService->getBuilder()->doAction($routingAction);
        $loaderService->getBuilder()->doAction($requestAction);
    }
}
