<?php

declare(strict_types=1);

namespace Duyler\Http\Attribute;

use Duyler\Framework\Build\AttributeHandlerInterface;
use Duyler\Framework\Build\AttributeInterface;
use Duyler\Http\AttributeHandler;
use Override;

readonly class Route implements AttributeInterface
{
    public function __construct(
        public string $method,
        public string $pattern,
        public string $name = '',
        public string $handler = '',
        public string $scenario = '',
        public array $where = [],
    ) {}

    #[Override]
    public function accept(AttributeHandlerInterface $handler, mixed $item): void
    {
        /** @var AttributeHandler $handler */
        $handler->handleRoute($this, $item);
    }
}
