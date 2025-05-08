<?php

declare(strict_types=1);

namespace Duyler\Http\Factory;

use Duyler\EventBus\Action\Context\FactoryContext;
use Duyler\Http\Action\Router;
use Duyler\Router\CurrentRoute;
use Psr\Http\Message\ServerRequestInterface;

class CreateRequestArgumentFactory
{
    public function __invoke(FactoryContext $context): ServerRequestInterface
    {
        /** @var ServerRequestInterface $request */
        $request = $context->getType('Http.CreateRawRequest');

        /** @var CurrentRoute $currentRoute */
        $currentRoute = $context->getType(Router::GetRoute);

        if ($currentRoute->status) {
            foreach ($currentRoute->attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
        }

        return $request;
    }
}
