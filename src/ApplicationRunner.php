<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Trigger;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Framework\Builder;
use HttpSoft\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ApplicationRunner
{
    private BusInterface $bus;

    public function __construct()
    {
        $builder = new Builder();
        $builder->loadPackages();
        $builder->loadBuild();

        $this->bus = $builder->build();
    }

    /**
     * @throws Throwable
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $trigger = new Trigger(
                id: 'Http.CreateRawRequest',
                status: ResultStatus::Success,
                data: $request,
                contract: ServerRequestInterface::class,
            );

            $this->bus->dispatchTrigger($trigger);
            $this->bus->run();

            if ($this->bus->resultIsExists('Http.PersistResponse')) {
                /** @var ResponseInterface $response */
                $response = $this->bus->getResult('Http.PersistResponse')->data;
                $this->bus->reset();
                return $response;
            }

            $this->bus->reset();
            return new EmptyResponse();
        } catch (Throwable $e) {
            $this->bus->reset();
            throw $e;
        }
    }
}
