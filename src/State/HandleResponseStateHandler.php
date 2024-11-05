<?php

declare(strict_types=1);

namespace Duyler\Http\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\Dto\Event;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Duyler\Http\Event\Response;
use Duyler\Http\RuntimeConfig;
use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\Http\PSR7Worker;

class HandleResponseStateHandler implements MainAfterStateHandlerInterface
{
    public function __construct(
        private PSR7Worker $worker,
        private RuntimeConfig $config,
    ) {}

    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        $result = $stateService->getResult('Http.PersistResponse');

        /** @var ResponseInterface $response */
        $response = $result->data;
        $this->worker->respond($response);

        if ($this->config->flushAfterResponseSent) {
            $stateService->flushSuccessLog();
        }

        $stateService->dispatchEvent(
            new Event(
                Response::ResponseHasBeenSent,
                $response,
            ),
        );
    }

    public function observed(StateContext $context): array
    {
        return ['Http.PersistResponse'];
    }
}
