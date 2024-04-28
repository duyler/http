<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\ActionBus\Dto\Result;
use Duyler\ActionBus\Enum\ResultStatus;
use Duyler\Router\RouteCollection;
use Duyler\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class StartRoutingAction
{
    public function __construct(
        private Router $router,
        private RouteCollection $routeCollection,
    ) {}

    public function __invoke(ServerRequestInterface $request): Result
    {
        $currentRoute = $this->router->startRouting(
            routeCollection: $this->routeCollection,
            serverRequest: $request,
        );

        return new Result(
            status: ResultStatus::Success,
            data: $currentRoute,
        );
    }
}
