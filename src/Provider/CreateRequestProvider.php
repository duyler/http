<?php

declare(strict_types=1);

namespace Duyler\Http\Provider;

use Duyler\DependencyInjection\Provider\AbstractProvider;
use Duyler\Http\RequestProvider;

class CreateRequestProvider extends AbstractProvider
{
    public function __construct(private RequestProvider $requestProvider) {}

    public function getArguments(): array
    {
        return [
            'request' => $this->requestProvider->get(),
        ];
    }
}
