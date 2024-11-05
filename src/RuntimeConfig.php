<?php

declare(strict_types=1);

namespace Duyler\Http;

final readonly class RuntimeConfig
{
    public function __construct(
        public bool $flushAfterResponseSent = true,
    ) {}
}
