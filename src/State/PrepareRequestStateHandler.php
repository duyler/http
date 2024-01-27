<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainBeginStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainBeginService;
use Duyler\EventBus\State\StateContext;
use Duyler\Http\RequestProvider;
use Override;
use Psr\Http\Message\ServerRequestInterface;

class PrepareRequestStateHandler implements MainBeginStateHandlerInterface
{
    public function __construct(private RequestProvider $requestProvider) {}

    #[Override]
    public function handle(StateMainBeginService $stateService, StateContext $context): void
    {
        $request = $this->requestProvider->get();
        $stateService->addSharedService(
            $request,
            [
                ServerRequestInterface::class => $request::class
            ]
        );
        $stateService->doExistsAction('Http.CreateRequest');
    }
}
