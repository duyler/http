<?php

declare(strict_types=1);

namespace Duyler\Http\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Psr\Http\Message\ServerRequestInterface;

class CreateRequestAction
{
    public function __invoke(ServerRequestInterface $request): Result
    {
        return new Result(
            status: ResultStatus::Success,
            data: $request
        );
    }
}
