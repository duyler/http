<?php

declare(strict_types=1);

namespace Duyler\Http;

use HttpSoft\Response\EmptyResponse;
use LogicException;
use Psr\Http\Message\ResponseInterface;

class ResponseTransmitter
{
    private ?ResponseInterface $response = null;

    public function transmit(ResponseInterface $response): void
    {
        if ($this->response !== null) {
            throw new LogicException('Response already transmitted');
        }

        $this->response = $response;
    }

    public function extract(): ResponseInterface
    {
        $response = $this->response ?? new EmptyResponse();
        $this->response = null;
        return $response;
    }

    public function isTransmitted(): bool
    {
        return $this->response === null;
    }

    public function reset(): void
    {
        $this->response = null;
    }
}
