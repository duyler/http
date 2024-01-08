<?php

declare(strict_types=1);

namespace Duyler\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseTransmitter
{
    public function __construct(private ResponseInterface $response) {}

    public function transmit(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
