<?php

declare(strict_types=1);

namespace Duyler\Http\Factory;

use Duyler\Router\CurrentRoute;
use Psr\Http\Message\ServerRequestInterface;

class CreateRequestArgumentFactory
{
    public function __construct(
        private ServerRequestInterface $request,
        private CurrentRoute $currentRoute,
    ) {}

    public function __invoke(): ServerRequestInterface
    {
        if ($this->currentRoute->status) {
            foreach ($this->currentRoute->attributes as $key => $value) {
                $this->request = $this->request->withAttribute($key, $value);
            }
        }

        return $this->request;
    }
}
