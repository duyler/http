<?php

declare(strict_types=1);

namespace Duyler\Http\Provider;

use Duyler\DependencyInjection\Provider\AbstractProvider;
use Duyler\Router\RouterConfig;
use HttpSoft\ServerRequest\ServerRequestCreator;

class RouterRequestProvider extends AbstractProvider
{
    public function __construct(private readonly RouterConfig $routerConfig)
    {
    }

    public function getParams(): array
    {
        return [
            'serverRequest' => ServerRequestCreator::create(),
            'routerConfig' => $this->routerConfig,
        ];
    }
}
