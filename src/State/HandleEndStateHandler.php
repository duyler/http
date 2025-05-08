<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainEndStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainEndService;
use Duyler\EventBus\State\StateContext;
use Duyler\Http\Event\Response;
use Duyler\Http\Exception\NotImplementedHttpException;
use Override;

class HandleEndStateHandler implements MainEndStateHandlerInterface
{
    #[Override]
    public function handle(StateMainEndService $stateService, StateContext $context): void
    {
        if ($stateService->resultIsExists('Http.CreateRawRequest')) {
            if (false === $stateService->resultIsExists(Response::ResponseCreated)) {
                throw new NotImplementedHttpException();
            }
        }
    }
}
