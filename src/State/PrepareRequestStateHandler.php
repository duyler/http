<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainStartStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainStartService;
use Duyler\Http\RequestProvider;
use Override;
use Psr\Http\Message\ServerRequestInterface;

class PrepareRequestStateHandler implements MainStartStateHandlerInterface
{
    public function __construct(private RequestProvider $requestProvider) {}

    #[Override]
    public function handle(StateMainStartService $stateService): void
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
