<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Override;
use Psr\Http\Message\ServerRequestInterface;

class ShareRequestStateHandler implements MainAfterStateHandlerInterface
{

    #[Override]
    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        $request = $stateService->getResultData();
        $stateService->addSharedService(
            $request,
            [
                ServerRequestInterface::class => $request::class
            ]
        );
    }

    #[Override]
    public function observed(StateContext $context): array
    {
        return ['Http.CreateRequest'];
    }
}
