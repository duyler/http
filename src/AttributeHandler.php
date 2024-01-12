<?php

declare(strict_types=1);

namespace Duyler\Http;

use Duyler\EventBus\Dto\Action;
use Duyler\Framework\Build\AttributeHandlerInterface;
use Duyler\Http\Attribute\Route as RouteAttribute;
use Duyler\Router\Route;
use Duyler\Router\RouteDefinition;
use Override;

class AttributeHandler implements AttributeHandlerInterface
{
    #[Override]
    public function getAttributeClasses(): array
    {
        return [
            RouteAttribute::class,
        ];
    }

    public function handleRoute(RouteAttribute $route, Action $action): void
    {
        /** @var RouteDefinition $definition */
        $definition = Route::{$route->method}($route->pattern);
        $definition->handler($route->handler);
        $definition->name($route->name);
        $definition->scenario($route->scenario);
        $definition->action($action->id);
        $definition->where($route->where);
    }
}
