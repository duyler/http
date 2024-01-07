<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainStartStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainStartService;
use Override;

class PrepareRequestStateHandler implements MainStartStateHandlerInterface
{
    #[Override]
    public function handle(StateMainStartService $stateService): void
    {
        $stateService->doExistsAction('Http.CreateRequest');
    }
}
