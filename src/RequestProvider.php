<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\Router\RouteCollection;
use Duyler\Router\Router;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class RequestProvider
{
    private ?ServerRequestInterface $request = null;

    public function __construct(
        private Router $router,
        private RouteCollection $routeCollection,
    ) {}

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

        $currentRoute = $this->router->startRouting($this->routeCollection, $request);

        foreach ($currentRoute->attributes as $key => $value) {
            $this->request = $this->request->withAttribute($key, $value);
        }

        $this->request = $this->request
            ->withAttribute('handler', $currentRoute->handler)
            ->withAttribute('target', $currentRoute->target)
            ->withAttribute('action', $currentRoute->action)
            ->withAttribute('language', $currentRoute->language)
        ;
    }

    public function reset(): void
    {
        $this->request = null;
    }
}
