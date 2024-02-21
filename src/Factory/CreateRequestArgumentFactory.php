<?php

declare(strict_types=1);

namespace Duyler\Http\Factory;

use Duyler\Router\CurrentRoute;
use Psr\Http\Message\ServerRequestInterface;

class CreateRequestArgumentFactory
{
    public function __invoke(ServerRequestInterface $request, CurrentRoute $currentRoute): ServerRequestInterface
    {
        if ($currentRoute->status) {
            foreach ($currentRoute->attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
        }

        return $request;
    }
}
