<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Framework\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Application
{
    private Runner $runner;

    public function __construct()
    {
        $builder = new Builder(Runner::NAME);
        $this->runner = $builder->build();
    }

    /**
     * @throws Throwable
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        return $this->runner->run($request);
    }
}
