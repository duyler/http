<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\Router;

readonly class StartRoutingAction
{
    public function __construct(private Router $router)
    {
    }

    public function __invoke(): Result
    {
        $result = $this->router->startRouting();

        return new Result(
            status: ResultStatus::Success,
            data: $result
        );
    }
}
