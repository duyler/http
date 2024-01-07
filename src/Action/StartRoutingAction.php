<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\RouteCollection;
use Duyler\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

readonly class StartRoutingAction
{
    public function __construct(
        private Router $router,
        private RouteCollection $routeCollection,
        private ServerRequestInterface $request,
    ) {}

    public function __invoke(): Result
    {
        $currentRoute = $this->router->startRouting($this->routeCollection, $this->request);

        return new Result(
            status: ResultStatus::Success,
            data: $currentRoute,
        );
    }
}
