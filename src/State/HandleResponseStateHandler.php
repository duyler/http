<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\Http\PSR7Worker;

class HandleResponseStateHandler implements MainAfterStateHandlerInterface
{
    public function __construct(
        private PSR7Worker $worker,
    ) {}

    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        $result = $stateService->getResult('Http.PersistResponse');

        /** @var ResponseInterface $response */
        $response = $result->data;
        $this->worker->respond($response);
    }

    public function observed(StateContext $context): array
    {
        return ['Http.PersistResponse'];
    }
}
