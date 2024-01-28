<?php

declare(strict_types=1);

namespace Duyler\Http;

use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class RequestProvider
{
    private ?ServerRequestInterface $request = null;

    public function get(): ?ServerRequestInterface
    {
        return $this->request;
    }

    public function build(ServerRequestInterface $request): void
    {
        if ($this->request !== null) {
            throw new LogicException('Request already built');
        }

        $this->request = $request;
    }

    public function reset(): void
    {
        $this->request = null;
    }
}
